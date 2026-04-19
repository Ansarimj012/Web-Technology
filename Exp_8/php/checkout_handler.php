<?php
/**
 * checkout_handler.php  (DB version)
 * Validates checkout form → saves order + items to MySQL.
 */
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/Validator.php';
require_once __DIR__ . '/../includes/OrderModel.php';
require_once __DIR__ . '/../includes/ProductModel.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../cart.php');
}

// Login is optional – guests may also checkout
$user = auth_user();

if (!csrf_verify()) {
    flash_set('error', 'Invalid form submission. Please try again.');
    redirect('../cart.php');
}

// ── Validate shipping + payment ───────────────────────────────────────────────
$fields = [
    'shipping_name','shipping_phone','shipping_address',
    'shipping_city','shipping_state','shipping_pin',
    'payment_method',
    'card_number','card_expiry','card_cvv','card_name',
    'upi_id',
];
$v = new Validator();
$v->load($fields)
  ->required('shipping_name',    'Full Name')
  ->minLength('shipping_name', 2,'Full Name')
  ->required('shipping_phone',   'Phone')
  ->phone('shipping_phone',      'Phone')
  ->required('shipping_address', 'Address')
  ->minLength('shipping_address',10,'Address')
  ->required('shipping_city',    'City')
  ->required('shipping_state',   'State')
  ->required('shipping_pin',     'PIN Code')
  ->pincode('shipping_pin')
  ->required('payment_method',   'Payment Method');

$method = $v->get('payment_method');

if ($method === 'card') {
    $v->required('card_number', 'Card Number')
      ->cardNumber('card_number')
      ->required('card_expiry', 'Expiry Date')
      ->cardExpiry('card_expiry')
      ->required('card_cvv', 'CVV')
      ->cvv('card_cvv')
      ->required('card_name', 'Name on Card')
      ->minLength('card_name', 2, 'Name on Card');
} elseif ($method === 'upi') {
    $v->required('upi_id', 'UPI ID');
    $upi = $v->get('upi_id');
    if ($upi && !preg_match('/^[\w.\-]+@[\w]+$/', $upi)) {
        // UPI format error – we access errors array indirectly via reflection trick
        // Simpler: just validate inline and redirect
        flash_set('errors', array_merge($v->errors(), ['upi_id' => 'Enter a valid UPI ID (e.g. name@upi).']));
        $old = $v->all();
        unset($old['card_number'], $old['card_cvv']);
        flash_set('old_input', $old);
        redirect('../cart.php');
    }
}

if ($v->fails()) {
    flash_set('errors', $v->errors());
    $old = $v->all();
    unset($old['card_number'], $old['card_cvv']);
    flash_set('old_input', $old);
    redirect('../cart.php');
}

// ── Parse cart from hidden field ──────────────────────────────────────────────
$cartJson = $_POST['cart_data'] ?? '[]';
$cartItems = json_decode($cartJson, true);

if (empty($cartItems) || !is_array($cartItems)) {
    flash_set('error', 'Your cart is empty. Please add products before checking out.');
    redirect('../cart.php');
}

// ── Validate each cart item against DB ───────────────────────────────────────
$productModel = new ProductModel();
$validatedItems = [];
$subtotal = 0;
$oldTotal = 0;

foreach ($cartItems as $item) {
    $productId = (int)($item['id'] ?? 0);
    $qty       = max(1, (int)($item['qty'] ?? 1));
    $dbProduct = $productModel->findById($productId);

    if (!$dbProduct) {
        // Product removed/deactivated – skip silently
        continue;
    }

    $lineTotal = (float)$dbProduct['price'] * $qty;
    $subtotal += $lineTotal;
    $oldTotal += (float)$dbProduct['old_price'] * $qty;

    $validatedItems[] = [
        'product_id'   => $dbProduct['id'],
        'product_name' => $dbProduct['name'],
        'unit_price'   => (float)$dbProduct['price'],
        'old_price'    => (float)$dbProduct['old_price'],
        'quantity'     => $qty,
        'line_total'   => $lineTotal,
    ];
}

if (empty($validatedItems)) {
    flash_set('error', 'No valid products in cart. Please try again.');
    redirect('../cart.php');
}

$discount       = round($oldTotal - $subtotal, 2);
$deliveryCharge = $subtotal > 500 ? 0.00 : 50.00;
$total          = round($subtotal + $deliveryCharge, 2);

// ── Generate unique order code ────────────────────────────────────────────────
$orderCode = 'ORD-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));

// ── Save to DB ────────────────────────────────────────────────────────────────
$orderModel = new OrderModel();

try {
    $orderId = $orderModel->placeOrder([
        'order_code'      => $orderCode,
        'user_id'         => $user ? (int)$user['id'] : null,
        'subtotal'        => $subtotal,
        'discount'        => $discount,
        'delivery_charge' => $deliveryCharge,
        'total'           => $total,
        'ship_name'       => $v->get('shipping_name'),
        'ship_phone'      => $v->get('shipping_phone'),
        'ship_address'    => $v->get('shipping_address'),
        'ship_city'       => $v->get('shipping_city'),
        'ship_state'      => $v->get('shipping_state'),
        'ship_pin'        => $v->get('shipping_pin'),
        'payment_method'  => $method,
    ], $validatedItems);
} catch (RuntimeException $e) {
    flash_set('error', $e->getMessage());
    redirect('../cart.php');
}

// ── Pass order details to success page ───────────────────────────────────────
flash_set('order_success', [
    'order_id'   => $orderCode,
    'shipping'   => [
        'name'    => $v->get('shipping_name'),
        'address' => $v->get('shipping_address'),
        'city'    => $v->get('shipping_city'),
        'pin'     => $v->get('shipping_pin'),
    ],
    'payment'    => $method,
    'placed_at'  => date('Y-m-d H:i:s'),
    'total'      => $total,
    'items'      => $validatedItems,
]);

redirect('../order_success.php');
