<?php
/**
 * OrderModel.php
 * All database operations related to orders and order items.
 */
require_once __DIR__ . '/Database.php';

class OrderModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::pdo();
    }

    // ── Place order (transactional) ───────────────────────────────────────────

    /**
     * Inserts an order + all its items in one atomic transaction.
     * Returns the new order ID on success, throws on failure.
     *
     * @param array $orderData  Keys: user_id, ship_*, payment_method,
     *                               subtotal, discount, delivery_charge, total
     * @param array $items      Each item: product_id, product_name,
     *                               unit_price, old_price, quantity, line_total
     */
    public function placeOrder(array $orderData, array $items): int
    {
        $this->db->beginTransaction();

        try {
            // 1. Insert order header
            $stmt = $this->db->prepare(
                'INSERT INTO orders
                   (order_code, user_id,
                    subtotal, discount, delivery_charge, total,
                    ship_name, ship_phone, ship_address,
                    ship_city, ship_state, ship_pin,
                    payment_method, payment_status, status)
                 VALUES
                   (:order_code, :user_id,
                    :subtotal, :discount, :delivery_charge, :total,
                    :ship_name, :ship_phone, :ship_address,
                    :ship_city, :ship_state, :ship_pin,
                    :payment_method, :payment_status, "confirmed")'
            );
            $stmt->execute([
                ':order_code'      => $orderData['order_code'],
                ':user_id'         => isset($orderData['user_id']) ? (int)$orderData['user_id'] : null,
                ':subtotal'        => $orderData['subtotal'],
                ':discount'        => $orderData['discount']        ?? 0,
                ':delivery_charge' => $orderData['delivery_charge'] ?? 0,
                ':total'           => $orderData['total'],
                ':ship_name'       => $orderData['ship_name'],
                ':ship_phone'      => $orderData['ship_phone'],
                ':ship_address'    => $orderData['ship_address'],
                ':ship_city'       => $orderData['ship_city'],
                ':ship_state'      => $orderData['ship_state'],
                ':ship_pin'        => $orderData['ship_pin'],
                ':payment_method'  => $orderData['payment_method'],
                ':payment_status'  => $orderData['payment_method'] === 'cod' ? 'pending' : 'paid',
            ]);
            $orderId = (int) $this->db->lastInsertId();

            // 2. Insert each item
            $itemStmt = $this->db->prepare(
                'INSERT INTO order_items
                   (order_id, product_id, product_name,
                    unit_price, old_price, quantity, line_total)
                 VALUES
                   (:order_id, :product_id, :product_name,
                    :unit_price, :old_price, :quantity, :line_total)'
            );
            foreach ($items as $item) {
                $itemStmt->execute([
                    ':order_id'    => $orderId,
                    ':product_id'  => $item['product_id'],
                    ':product_name'=> $item['product_name'],
                    ':unit_price'  => $item['unit_price'],
                    ':old_price'   => $item['old_price'],
                    ':quantity'    => $item['quantity'],
                    ':line_total'  => $item['line_total'],
                ]);
            }

            $this->db->commit();
            return $orderId;

        } catch (Throwable $e) {
            $this->db->rollBack();
            error_log('Order placement failed: ' . $e->getMessage());
            throw new RuntimeException('Could not save your order. Please try again.');
        }
    }

    // ── Finders ──────────────────────────────────────────────────────────────

    /**
     * Fetch a single order with its items by order_code.
     */
    public function findByCode(string $orderCode): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM orders WHERE order_code = :code LIMIT 1'
        );
        $stmt->execute([':code' => $orderCode]);
        $order = $stmt->fetch();
        if (!$order) return null;

        $order['items'] = $this->getItems((int) $order['id']);
        return $order;
    }

    /**
     * Fetch all orders for a user (newest first).
     */
    public function getOrdersByUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT id, order_code, total, status, payment_method, placed_at
               FROM orders
              WHERE user_id = :uid
              ORDER BY placed_at DESC'
        );
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Fetch order items for a given order ID.
     */
    public function getItems(int $orderId): array
    {
        $stmt = $this->db->prepare(
            'SELECT oi.*, p.image_path
               FROM order_items oi
               LEFT JOIN products p ON p.id = oi.product_id
              WHERE oi.order_id = :oid'
        );
        $stmt->execute([':oid' => $orderId]);
        return $stmt->fetchAll();
    }
}
