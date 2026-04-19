<?php
/**
 * order_success.php – Shown after a successful DB-backed checkout.
 */
require_once __DIR__ . '/includes/session.php';

$order = flash_get('order_success');
if (!$order) {
    redirect('index.php');
}
$payLabel = ['card' => '💳 Credit / Debit Card', 'upi' => '📱 UPI', 'cod' => '💵 Cash on Delivery'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Confirmed – Online Store</title>
  <link rel="stylesheet" href="styles/shared.css">
  <style>
    body { display:flex; flex-direction:column; align-items:center; justify-content:center;
           min-height:100vh; background:#f4f6f8; font-family:'Poppins',sans-serif; padding:24px; }
    .success-box { background:white; border-radius:20px; padding:40px 36px;
                   max-width:560px; width:100%; text-align:center;
                   box-shadow:0 8px 32px rgba(0,0,0,.10); }
    .tick { font-size:72px; margin-bottom:12px; animation:pop .5s ease; }
    @keyframes pop { 0%{transform:scale(0)} 70%{transform:scale(1.15)} 100%{transform:scale(1)} }
    h2 { font-size:22px; font-weight:700; color:#111; margin-bottom:4px; }
    .order-id { color:#888; font-size:13px; margin-bottom:20px; }
    .section { background:#f8f9fa; border-radius:12px; padding:16px 20px;
               text-align:left; margin-bottom:16px; font-size:13px; }
    .section h4 { font-size:12px; font-weight:700; text-transform:uppercase;
                  letter-spacing:.5px; color:#aaa; margin-bottom:10px; }
    .row { display:flex; justify-content:space-between; padding:4px 0;
           border-bottom:1px solid #f0f0f0; }
    .row:last-child { border-bottom:none; }
    .row strong { color:#333; }
    /* Items table */
    .items-table { width:100%; border-collapse:collapse; font-size:13px; }
    .items-table th { text-align:left; font-size:11px; text-transform:uppercase;
                      color:#aaa; font-weight:600; padding:6px 0; border-bottom:1px solid #eee; }
    .items-table td { padding:8px 0; border-bottom:1px solid #f5f5f5; vertical-align:middle; }
    .items-table td:last-child { text-align:right; font-weight:600; color:#00adb5; }
    .total-row { font-size:15px; font-weight:700; }
    .go-btn { display:inline-block; padding:13px 32px; background:#00adb5; color:white;
              border-radius:10px; text-decoration:none; font-weight:700; font-size:15px;
              transition:background .2s; margin-top:8px; }
    .go-btn:hover { background:#008c93; }
    .badge { display:inline-block; background:#f0fff4; color:#22543d;
             border:1px solid #9ae6b4; border-radius:20px; font-size:12px;
             padding:3px 12px; margin-bottom:12px; }
  </style>
</head>
<body>
<div class="success-box">
  <div class="tick">🎉</div>
  <div class="badge">✅ Saved to Database</div>
  <h2>Order Confirmed!</h2>
  <p class="order-id">Order ID: <strong><?= htmlspecialchars($order['order_id']) ?></strong></p>

  <!-- Shipping -->
  <div class="section">
    <h4>📦 Shipping Address</h4>
    <div class="row"><span>Name</span>    <strong><?= htmlspecialchars($order['shipping']['name']) ?></strong></div>
    <div class="row"><span>Address</span> <strong><?= htmlspecialchars($order['shipping']['address']) ?></strong></div>
    <div class="row"><span>City / PIN</span> <strong><?= htmlspecialchars($order['shipping']['city']) ?> – <?= htmlspecialchars($order['shipping']['pin']) ?></strong></div>
    <div class="row"><span>Payment</span> <strong><?= $payLabel[$order['payment']] ?? strtoupper($order['payment']) ?></strong></div>
    <div class="row"><span>Placed at</span> <strong><?= htmlspecialchars($order['placed_at']) ?></strong></div>
    <div class="row"><span>Expected Delivery</span> <strong>3–5 business days</strong></div>
  </div>

  <!-- Items -->
  <?php if (!empty($order['items'])): ?>
  <div class="section">
    <h4>🛒 Items Ordered</h4>
    <table class="items-table">
      <thead><tr><th>Product</th><th>Qty</th><th>Total</th></tr></thead>
      <tbody>
        <?php foreach ($order['items'] as $item): ?>
        <tr>
          <td><?= htmlspecialchars($item['product_name']) ?></td>
          <td>× <?= (int)$item['quantity'] ?></td>
          <td>₹<?= number_format($item['line_total'], 2) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>

  <!-- Total -->
  <div class="section" style="padding:14px 20px;">
    <div class="row total-row">
      <span>Grand Total</span>
      <strong style="color:#00adb5;">₹<?= number_format($order['total'] ?? 0, 2) ?></strong>
    </div>
  </div>

  <a href="products.php" class="go-btn">Continue Shopping →</a>
</div>

<script src="js/store.js"></script>
<script>Store.saveCart([]);</script>
</body>
</html>
