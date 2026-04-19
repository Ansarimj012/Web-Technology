<?php
/**
 * cart.php – Cart page with PHP checkout form & validation.
 */
require_once __DIR__ . '/includes/session.php';

$errors     = flash_get('errors', []);
$old        = flash_get('old_input', []);
$errorMsg   = flash_get('error');
$user       = auth_user();

function fc(array $errors, string $field): string {
    return isset($errors[$field]) ? ' input-error' : '';
}
function old_val(array $old, string $field, string $default = ''): string {
    return htmlspecialchars($old[$field] ?? $default);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Cart – Online Store</title>
  <link rel="stylesheet" href="styles/shared.css">
  <link rel="stylesheet" href="styles/cart.css">
  <style>
    /* ── Checkout form ─────────────────────────────────────────────── */
    .checkout-section {
      background: white; border-radius: 16px;
      padding: 28px; margin-top: 28px;
      box-shadow: 0 2px 16px rgba(0,0,0,0.07);
    }
    .checkout-section h3 { font-size: 18px; font-weight: 700; margin-bottom: 18px; color: #111; }
    .checkout-section h4 { font-size: 14px; font-weight: 600; color: #555; margin: 20px 0 10px; text-transform: uppercase; letter-spacing: .5px; }

    .form-group-co { margin-bottom: 14px; }
    .form-row-co  { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .form-group-co label { display: block; font-size: 12px; font-weight: 600; color: #666; margin-bottom: 5px; }
    .form-group-co input, .form-group-co select {
      width: 100%; padding: 10px 12px; border: 1.5px solid #e0e0e0;
      border-radius: 8px; font-family: 'Poppins', sans-serif; font-size: 13px;
      color: #333; transition: border-color .2s, box-shadow .2s;
    }
    .form-group-co input:focus, .form-group-co select:focus {
      outline: none; border-color: #00adb5; box-shadow: 0 0 0 3px rgba(0,173,181,.12);
    }
    .input-error { border-color: #e53e3e !important; background: #fff5f5 !important; }
    .co-field-error { display: block; color: #e53e3e; font-size: 11px; margin-top: 3px; }

    /* Payment tabs */
    .pay-tabs { display: flex; gap: 8px; margin-bottom: 16px; flex-wrap: wrap; }
    .pay-tab {
      flex: 1; min-width: 90px; padding: 10px 8px; border: 2px solid #e0e0e0;
      border-radius: 10px; cursor: pointer; text-align: center; font-size: 13px;
      font-weight: 600; color: #555; background: white; transition: all .2s;
    }
    .pay-tab.active { border-color: #00adb5; color: #00adb5; background: #f0fdfe; }
    .pay-panel { display: none; }
    .pay-panel.active { display: block; }

    .card-icons { display: flex; gap: 8px; margin-bottom: 12px; font-size: 22px; }

    .co-submit-btn {
      width: 100%; padding: 14px; background: #00adb5; color: white;
      border: none; border-radius: 10px; font-family: 'Poppins', sans-serif;
      font-size: 15px; font-weight: 700; cursor: pointer; margin-top: 10px;
      transition: background .2s, transform .15s;
    }
    .co-submit-btn:hover { background: #008c93; transform: translateY(-2px); }
    .co-submit-btn:disabled { background: #b2e3e5; cursor: not-allowed; transform: none; }

    .secure-badge { text-align: center; font-size: 12px; color: #aaa; margin-top: 10px; }
    .login-prompt {
      background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px;
      padding: 14px 18px; font-size: 14px; color: #92400e; margin-top: 16px;
    }
    .login-prompt a { color: #00adb5; font-weight: 600; text-decoration: none; }
    .php-badge {
      display: inline-block; background: #6366f1; color: #fff;
      font-size: 10px; font-weight: 700; padding: 1px 7px; border-radius: 20px;
      margin-left: 6px; vertical-align: middle;
    }
    @media(max-width:600px) { .form-row-co { grid-template-columns: 1fr; } }
  </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
  <div class="logo"><a href="index.php"><span class="brand">🛍️ Online Store</span></a></div>
  <div class="search-wrapper">
    <input type="text" id="search-input" placeholder="Search products…">
    <span class="search-icon">🔍</span>
    <div id="search-dropdown"></div>
  </div>
  <div class="nav-right">
    <?php if ($user): ?>
      <span style="color:white;font-weight:600;"><?= htmlspecialchars(explode(' ', $user['name'])[0]) ?></span>
      <a href="php/logout.php" class="nav-btn" style="color:white;">Logout</a>
    <?php else: ?>
      <a href="login.php" id="nav-login-link">Login</a>
    <?php endif; ?>
    <a href="products.php">Products</a>
    <a href="cart.php" class="cart-link">🛒 Cart <span id="cart-badge">0</span></a>
  </div>
</nav>

<div class="cart-page">

  <!-- CART ITEMS -->
  <div class="cart-left">
    <h2 class="section-title">🛒 My Cart</h2>
    <div id="cart-items-container"></div>

    <!-- ── CHECKOUT FORM ──────────────────────────────────────────── -->
    <div class="checkout-section" id="checkout-form-section" style="display:none;">
      <h3>📦 Checkout <span class="php-badge">PHP</span></h3>

      <?php if ($errorMsg): ?>
        <div style="background:#fff5f5;color:#e53e3e;border:1px solid #fed7d7;border-radius:8px;padding:11px 14px;font-size:13px;margin-bottom:16px;">
          ❌ <?= htmlspecialchars($errorMsg) ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="php/checkout_handler.php" novalidate id="checkout-form">
        <?= csrf_field() ?>
        <input type="hidden" name="cart_data" id="hidden-cart-data">

        <!-- SHIPPING -->
        <h4>🏠 Shipping Address</h4>
        <div class="form-row-co">
          <div class="form-group-co">
            <label>Full Name *</label>
            <input type="text" name="shipping_name" placeholder="John Doe"
              value="<?= old_val($old, 'shipping_name', $user['name'] ?? '') ?>"
              class="<?= fc($errors, 'shipping_name') ?>">
            <?php if (!empty($errors['shipping_name'])): ?>
              <span class="co-field-error">⚠ <?= htmlspecialchars($errors['shipping_name']) ?></span>
            <?php endif; ?>
          </div>
          <div class="form-group-co">
            <label>Phone *</label>
            <input type="tel" name="shipping_phone" placeholder="9XXXXXXXXX" maxlength="10"
              value="<?= old_val($old, 'shipping_phone') ?>"
              class="<?= fc($errors, 'shipping_phone') ?>">
            <?php if (!empty($errors['shipping_phone'])): ?>
              <span class="co-field-error">⚠ <?= htmlspecialchars($errors['shipping_phone']) ?></span>
            <?php endif; ?>
          </div>
        </div>

        <div class="form-group-co">
          <label>Address (House No., Street, Area) *</label>
          <input type="text" name="shipping_address" placeholder="123, MG Road, Indiranagar"
            value="<?= old_val($old, 'shipping_address') ?>"
            class="<?= fc($errors, 'shipping_address') ?>">
          <?php if (!empty($errors['shipping_address'])): ?>
            <span class="co-field-error">⚠ <?= htmlspecialchars($errors['shipping_address']) ?></span>
          <?php endif; ?>
        </div>

        <div class="form-row-co">
          <div class="form-group-co">
            <label>City *</label>
            <input type="text" name="shipping_city" placeholder="Bengaluru"
              value="<?= old_val($old, 'shipping_city') ?>"
              class="<?= fc($errors, 'shipping_city') ?>">
            <?php if (!empty($errors['shipping_city'])): ?>
              <span class="co-field-error">⚠ <?= htmlspecialchars($errors['shipping_city']) ?></span>
            <?php endif; ?>
          </div>
          <div class="form-group-co">
            <label>State *</label>
            <input type="text" name="shipping_state" placeholder="Karnataka"
              value="<?= old_val($old, 'shipping_state') ?>"
              class="<?= fc($errors, 'shipping_state') ?>">
            <?php if (!empty($errors['shipping_state'])): ?>
              <span class="co-field-error">⚠ <?= htmlspecialchars($errors['shipping_state']) ?></span>
            <?php endif; ?>
          </div>
        </div>

        <div class="form-group-co" style="max-width:180px;">
          <label>PIN Code *</label>
          <input type="text" name="shipping_pin" placeholder="560001" maxlength="6"
            value="<?= old_val($old, 'shipping_pin') ?>"
            class="<?= fc($errors, 'shipping_pin') ?>">
          <?php if (!empty($errors['shipping_pin'])): ?>
            <span class="co-field-error">⚠ <?= htmlspecialchars($errors['shipping_pin']) ?></span>
          <?php endif; ?>
        </div>

        <!-- PAYMENT -->
        <h4>💳 Payment Method</h4>
        <input type="hidden" name="payment_method" id="payment_method_input"
          value="<?= old_val($old, 'payment_method', 'card') ?>">

        <div class="pay-tabs">
          <div class="pay-tab <?= (old_val($old, 'payment_method', 'card') === 'card') ? 'active' : '' ?>" onclick="switchPay('card')">💳 Card</div>
          <div class="pay-tab <?= (old_val($old, 'payment_method', 'card') === 'upi')  ? 'active' : '' ?>" onclick="switchPay('upi')">📱 UPI</div>
          <div class="pay-tab <?= (old_val($old, 'payment_method', 'card') === 'cod')  ? 'active' : '' ?>" onclick="switchPay('cod')">💵 COD</div>
        </div>

        <!-- Card Panel -->
        <div id="panel-card" class="pay-panel <?= (old_val($old, 'payment_method', 'card') === 'card') ? 'active' : '' ?>">
          <div class="card-icons">💳 🏦 🛡️</div>
          <div class="form-group-co">
            <label>Card Number *</label>
            <input type="text" name="card_number" placeholder="1234 5678 9012 3456"
              maxlength="19" oninput="formatCard(this)"
              value="<?= old_val($old, 'card_number') ?>"
              class="<?= fc($errors, 'card_number') ?>">
            <?php if (!empty($errors['card_number'])): ?>
              <span class="co-field-error">⚠ <?= htmlspecialchars($errors['card_number']) ?></span>
            <?php endif; ?>
          </div>
          <div class="form-row-co">
            <div class="form-group-co">
              <label>Expiry Date (MM/YY) *</label>
              <input type="text" name="card_expiry" placeholder="08/27" maxlength="5"
                oninput="formatExpiry(this)"
                value="<?= old_val($old, 'card_expiry') ?>"
                class="<?= fc($errors, 'card_expiry') ?>">
              <?php if (!empty($errors['card_expiry'])): ?>
                <span class="co-field-error">⚠ <?= htmlspecialchars($errors['card_expiry']) ?></span>
              <?php endif; ?>
            </div>
            <div class="form-group-co">
              <label>CVV *</label>
              <input type="password" name="card_cvv" placeholder="•••" maxlength="4"
                class="<?= fc($errors, 'card_cvv') ?>">
              <?php if (!empty($errors['card_cvv'])): ?>
                <span class="co-field-error">⚠ <?= htmlspecialchars($errors['card_cvv']) ?></span>
              <?php endif; ?>
            </div>
          </div>
          <div class="form-group-co">
            <label>Name on Card *</label>
            <input type="text" name="card_name" placeholder="John Doe"
              value="<?= old_val($old, 'card_name') ?>"
              class="<?= fc($errors, 'card_name') ?>">
            <?php if (!empty($errors['card_name'])): ?>
              <span class="co-field-error">⚠ <?= htmlspecialchars($errors['card_name']) ?></span>
            <?php endif; ?>
          </div>
        </div>

        <!-- UPI Panel -->
        <div id="panel-upi" class="pay-panel <?= (old_val($old, 'payment_method', 'card') === 'upi') ? 'active' : '' ?>">
          <div class="form-group-co">
            <label>UPI ID *</label>
            <input type="text" name="upi_id" placeholder="yourname@upi"
              value="<?= old_val($old, 'upi_id') ?>"
              class="<?= fc($errors, 'upi_id') ?>">
            <?php if (!empty($errors['upi_id'])): ?>
              <span class="co-field-error">⚠ <?= htmlspecialchars($errors['upi_id']) ?></span>
            <?php endif; ?>
            <span style="font-size:11px;color:#aaa;margin-top:4px;display:block;">e.g. name@okaxis, 9876543210@paytm</span>
          </div>
        </div>

        <!-- COD Panel -->
        <div id="panel-cod" class="pay-panel <?= (old_val($old, 'payment_method', 'card') === 'cod') ? 'active' : '' ?>">
          <div style="background:#f0fff4;border-radius:10px;padding:14px 18px;font-size:13px;color:#276749;">
            💵 Pay in cash when your order is delivered. No extra charges.
          </div>
        </div>

        <button type="submit" class="co-submit-btn" id="co-submit">
          🔒 Place Order Securely
        </button>
        <p class="secure-badge">🔐 256-bit SSL encrypted · PHP server-side validated</p>
      </form>
    </div>
    <!-- end checkout form -->

  </div><!-- .cart-left -->

  <!-- ORDER SUMMARY -->
  <div class="cart-right">
    <div class="order-summary">
      <h3>Order Summary</h3>
      <div class="summary-row"><span>Subtotal</span><span id="summary-subtotal">₹0</span></div>
      <div class="summary-row"><span>Discount</span><span id="summary-discount" style="color:#22a06b;">-₹0</span></div>
      <div class="summary-row"><span>Delivery</span><span id="summary-delivery" style="color:#22a06b;">FREE</span></div>
      <div class="summary-divider"></div>
      <div class="summary-row total-row"><span>Total</span><span id="summary-total">₹0</span></div>
      <div class="savings-msg" id="savings-msg" style="display:none;"></div>
      <button class="checkout-btn" id="proceed-checkout-btn" onclick="showCheckoutForm()">Proceed to Checkout →</button>
      <a href="products.php" class="continue-shopping">← Continue Shopping</a>

      <?php if (!$user): ?>
        <div class="login-prompt" style="background:#f0fff4;border:1px solid #9ae6b4;border-radius:10px;padding:12px 16px;font-size:13px;color:#276749;margin-top:10px;">
          💡 <a href="login.php" style="color:#00adb5;font-weight:600;">Login</a> for faster checkout &amp; order tracking. <em>Not required.</em>
        </div>
      <?php endif; ?>
    </div>
  </div>

</div><!-- .cart-page -->

<script src="js/store.js"></script>
<script>
  // ── Cart render (same as before) ──────────────────────────────────────────
  function renderCart() {
    const cart = Store.getCart();
    const container = document.getElementById('cart-items-container');
    if (!cart.length) {
      container.innerHTML = `
        <div class="empty-cart">
          <div style="font-size:72px;">🛒</div>
          <h3>Your cart is empty</h3>
          <p>Add products to your cart to see them here.</p>
          <a href="products.php" class="checkout-btn" style="display:inline-block;text-decoration:none;text-align:center;">Browse Products</a>
        </div>`;
      updateSummary([], 0, 0);
      return;
    }
    container.innerHTML = cart.map(item => `
      <div class="cart-item" id="cart-item-${item.id}">
        <img src="${item.img}" alt="${item.name}">
        <div class="cart-details">
          <h3>${item.name}</h3>
          <p class="cart-price">₹${item.price.toLocaleString()}</p>
          <p class="cart-save">You save ₹${(item.oldPrice - item.price).toLocaleString()} on this item</p>
          <div class="qty-controls">
            <button class="qty-btn" onclick="changeQty(${item.id}, -1)">−</button>
            <span class="qty-display" id="qty-${item.id}">${item.qty}</span>
            <button class="qty-btn" onclick="changeQty(${item.id}, 1)">+</button>
          </div>
          <p class="item-total">Item Total: <strong>₹${(item.price * item.qty).toLocaleString()}</strong></p>
          <button class="remove-btn" onclick="removeItem(${item.id})">🗑 Remove</button>
        </div>
      </div>`).join('');
    const subtotal = cart.reduce((s, i) => s + i.oldPrice * i.qty, 0);
    const total    = cart.reduce((s, i) => s + i.price   * i.qty, 0);
    updateSummary(cart, subtotal, total);
  }

  function updateSummary(cart, subtotal, total) {
    const discount = subtotal - total;
    document.getElementById('summary-subtotal').textContent = `₹${(subtotal || 0).toLocaleString()}`;
    document.getElementById('summary-discount').textContent = `-₹${(discount || 0).toLocaleString()}`;
    document.getElementById('summary-total').textContent    = `₹${(total    || 0).toLocaleString()}`;
    document.getElementById('summary-delivery').textContent = total > 500 ? 'FREE' : '₹50';
    const msg = document.getElementById('savings-msg');
    if (discount > 0) { msg.style.display = 'block'; msg.textContent = `🎉 You're saving ₹${discount.toLocaleString()}!`; }
  }

  function changeQty(id, delta) {
    const cart = Store.getCart();
    const item = cart.find(i => i.id === id);
    if (!item) return;
    const newQty = item.qty + delta;
    if (newQty < 1) { removeItem(id); return; }
    Store.updateQty(id, newQty);
    renderCart();
  }
  function removeItem(id) { Store.removeFromCart(id); showToast('Item removed.'); renderCart(); }

  // ── Checkout form toggle ──────────────────────────────────────────────────
  function showCheckoutForm() {
    const cart = Store.getCart();
    if (!cart.length) { showToast('Your cart is empty!', 'error'); return; }
    document.getElementById('checkout-form-section').style.display = 'block';
    document.getElementById('proceed-checkout-btn').style.display = 'none';
    document.getElementById('checkout-form-section').scrollIntoView({ behavior: 'smooth' });
    // Pass cart as hidden field for server
    document.getElementById('hidden-cart-data').value = JSON.stringify(cart);
  }

  // Auto-show if redirected back with errors
  <?php if (!empty($errors)): ?>
  document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('checkout-form-section').style.display = 'block';
    document.getElementById('proceed-checkout-btn').style.display = 'none';
  });
  <?php endif; ?>

  // ── Payment tabs ──────────────────────────────────────────────────────────
  function switchPay(method) {
    document.querySelectorAll('.pay-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.pay-panel').forEach(p => p.classList.remove('active'));
    document.getElementById('payment_method_input').value = method;
    event.currentTarget.classList.add('active');
    document.getElementById('panel-' + method).classList.add('active');
  }

  // ── Card formatting helpers ───────────────────────────────────────────────
  function formatCard(input) {
    let v = input.value.replace(/\D/g, '').substring(0, 16);
    input.value = v.replace(/(.{4})/g, '$1 ').trim();
  }
  function formatExpiry(input) {
    let v = input.value.replace(/\D/g, '').substring(0, 4);
    if (v.length > 2) v = v.substring(0, 2) + '/' + v.substring(2);
    input.value = v;
  }

  // ── Client pre-validation ─────────────────────────────────────────────────
  document.getElementById('checkout-form').addEventListener('submit', function(e) {
    let valid = true;
    document.querySelectorAll('.co-field-error').forEach(el => el.textContent = '');

    const get   = name => this.querySelector(`[name="${name}"]`)?.value.trim() || '';
    const error = (name, msg) => {
      valid = false;
      const input = this.querySelector(`[name="${name}"]`);
      if (!input) return;
      input.classList.add('input-error');
      let span = input.closest('.form-group-co')?.querySelector('.co-field-error');
      if (!span) { span = document.createElement('span'); span.className = 'co-field-error'; input.closest('.form-group-co').appendChild(span); }
      span.textContent = '⚠ ' + msg;
    };

    if (!get('shipping_name') || get('shipping_name').length < 2) error('shipping_name', 'Full name is required.');
    if (!/^[6-9]\d{9}$/.test(get('shipping_phone'))) error('shipping_phone', 'Valid 10-digit phone required.');
    if (get('shipping_address').length < 10) error('shipping_address', 'Enter your full address (min 10 chars).');
    if (!get('shipping_city'))  error('shipping_city',  'City is required.');
    if (!get('shipping_state')) error('shipping_state', 'State is required.');
    if (!/^\d{6}$/.test(get('shipping_pin'))) error('shipping_pin', 'Valid 6-digit PIN required.');

    const method = get('payment_method');
    if (method === 'card') {
      const cn = get('card_number').replace(/\s/g, '');
      if (!/^\d{16}$/.test(cn)) error('card_number', 'Card number must be 16 digits.');
      if (!/^(0[1-9]|1[0-2])\/\d{2}$/.test(get('card_expiry'))) error('card_expiry', 'Use MM/YY format.');
      if (!/^\d{3,4}$/.test(get('card_cvv'))) error('card_cvv', 'CVV is 3–4 digits.');
      if (!get('card_name')) error('card_name', 'Name on card is required.');
    } else if (method === 'upi') {
      if (!/^[\w.\-]+@[\w]+$/.test(get('upi_id'))) error('upi_id', 'Enter a valid UPI ID (e.g. name@upi).');
    }

    if (!valid) { e.preventDefault(); return; }
    document.getElementById('hidden-cart-data').value = JSON.stringify(Store.getCart());
    const btn = document.getElementById('co-submit');
    btn.textContent = 'Placing Order…'; btn.disabled = true;
  });

  renderCart();
</script>
</body>
</html>
