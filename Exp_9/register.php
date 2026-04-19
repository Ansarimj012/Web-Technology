<?php
/**
 * register.php – PHP-powered registration page.
 */
require_once __DIR__ . '/includes/session.php';

if (auth_check()) {
    redirect('index.php');
}

$errors   = flash_get('errors', []);
$old      = flash_get('old_input', []);
$errorMsg = flash_get('error');

function fc(array $errors, string $field): string {
    return isset($errors[$field]) ? ' input-error' : '';
}
function old(array $old, string $field): string {
    return htmlspecialchars($old[$field] ?? '');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register – Online Store</title>
  <link rel="stylesheet" href="styles/shared.css">
  <link rel="stylesheet" href="styles/auth.css">
  <style>
    .input-error { border-color: #e53e3e !important; background: #fff5f5 !important; }
    .php-badge {
      display: inline-block; background: #6366f1; color: #fff;
      font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 20px;
      letter-spacing: 0.5px; margin-left: 8px; vertical-align: middle;
    }
  </style>
</head>
<body>

<nav class="navbar">
  <div class="logo"><a href="index.php"><span class="brand">🛍️ Online Store</span></a></div>
  <div class="search-wrapper">
    <input type="text" id="search-input" placeholder="Search products…">
    <span class="search-icon">🔍</span>
    <div id="search-dropdown"></div>
  </div>
  <div class="nav-right">
    <a href="login.php">Login</a>
    <a href="products.php">Products</a>
    <a href="cart.php" class="cart-link">🛒 Cart <span id="cart-badge">0</span></a>
  </div>
</nav>

<div class="auth-page">

  <div class="auth-panel left-panel">
    <div class="auth-illustration">
      <div style="font-size:80px;margin-bottom:24px;">🎁</div>
      <h2>Join Us Today!</h2>
      <p>Create your account and start enjoying exclusive benefits.</p>
      <ul class="perks">
        <li>🎉 Welcome discount on first order</li>
        <li>📦 Free delivery on orders above ₹500</li>
        <li>🔔 Early access to new products</li>
        <li>💳 Secure &amp; fast checkout</li>
      </ul>
    </div>
  </div>

  <div class="auth-panel right-panel">
    <div class="auth-box">
      <h2>Create Account <span class="php-badge">PHP</span></h2>
      <p class="auth-subtitle">Fill in the details to get started</p>

      <?php if ($errorMsg): ?>
        <div class="error-msg">❌ <?= htmlspecialchars($errorMsg) ?></div>
      <?php endif; ?>

      <form method="POST" action="php/register_handler.php" novalidate id="register-form">
        <?= csrf_field() ?>

        <!-- Name + Phone -->
        <div class="form-row">
          <div class="form-group">
            <label for="name">Full Name *</label>
            <input type="text" id="name" name="name" placeholder="John Doe"
              value="<?= old($old, 'name') ?>" class="<?= fc($errors, 'name') ?>" autocomplete="name">
            <?php if (!empty($errors['name'])): ?>
              <span class="field-error">⚠ <?= htmlspecialchars($errors['name']) ?></span>
            <?php else: ?><span class="field-error"></span><?php endif; ?>
          </div>
          <div class="form-group">
            <label for="phone">Phone Number *</label>
            <input type="tel" id="phone" name="phone" placeholder="9XXXXXXXXX" maxlength="10"
              value="<?= old($old, 'phone') ?>" class="<?= fc($errors, 'phone') ?>" autocomplete="tel">
            <?php if (!empty($errors['phone'])): ?>
              <span class="field-error">⚠ <?= htmlspecialchars($errors['phone']) ?></span>
            <?php else: ?><span class="field-error"></span><?php endif; ?>
          </div>
        </div>

        <!-- Email -->
        <div class="form-group">
          <label for="email">Email Address *</label>
          <input type="email" id="email" name="email" placeholder="you@example.com"
            value="<?= old($old, 'email') ?>" class="<?= fc($errors, 'email') ?>" autocomplete="email">
          <?php if (!empty($errors['email'])): ?>
            <span class="field-error">⚠ <?= htmlspecialchars($errors['email']) ?></span>
          <?php else: ?><span class="field-error"></span><?php endif; ?>
        </div>

        <!-- Password + Confirm -->
        <div class="form-row">
          <div class="form-group">
            <label for="reg-password">Password *</label>
            <div class="password-wrapper">
              <input type="password" id="reg-password" name="password" placeholder="Min. 6 characters"
                class="<?= fc($errors, 'password') ?>" oninput="checkStrength()" autocomplete="new-password">
              <button type="button" class="toggle-pw" onclick="togglePw('reg-password', this)">👁</button>
            </div>
            <div class="strength-bar" id="strength-bar"><div id="strength-fill"></div></div>
            <?php if (!empty($errors['password'])): ?>
              <span class="field-error">⚠ <?= htmlspecialchars($errors['password']) ?></span>
            <?php else: ?><span class="field-error"></span><?php endif; ?>
          </div>
          <div class="form-group">
            <label for="reg-confirm">Confirm Password *</label>
            <div class="password-wrapper">
              <input type="password" id="reg-confirm" name="confirm_password" placeholder="Re-enter password"
                class="<?= fc($errors, 'confirm_password') ?>" autocomplete="new-password">
              <button type="button" class="toggle-pw" onclick="togglePw('reg-confirm', this)">👁</button>
            </div>
            <?php if (!empty($errors['confirm_password'])): ?>
              <span class="field-error">⚠ <?= htmlspecialchars($errors['confirm_password']) ?></span>
            <?php else: ?><span class="field-error"></span><?php endif; ?>
          </div>
        </div>

        <!-- City + State -->
        <div class="form-row">
          <div class="form-group">
            <label for="city">City *</label>
            <input type="text" id="city" name="city" placeholder="Bengaluru"
              value="<?= old($old, 'city') ?>" class="<?= fc($errors, 'city') ?>">
            <?php if (!empty($errors['city'])): ?>
              <span class="field-error">⚠ <?= htmlspecialchars($errors['city']) ?></span>
            <?php else: ?><span class="field-error"></span><?php endif; ?>
          </div>
          <div class="form-group">
            <label for="state">State *</label>
            <input type="text" id="state" name="state" placeholder="Karnataka"
              value="<?= old($old, 'state') ?>" class="<?= fc($errors, 'state') ?>">
            <?php if (!empty($errors['state'])): ?>
              <span class="field-error">⚠ <?= htmlspecialchars($errors['state']) ?></span>
            <?php else: ?><span class="field-error"></span><?php endif; ?>
          </div>
        </div>

        <!-- PIN -->
        <div class="form-group">
          <label for="pin">PIN Code *</label>
          <input type="text" id="pin" name="pin" placeholder="560001" maxlength="6"
            value="<?= old($old, 'pin') ?>" class="<?= fc($errors, 'pin') ?>">
          <?php if (!empty($errors['pin'])): ?>
            <span class="field-error">⚠ <?= htmlspecialchars($errors['pin']) ?></span>
          <?php else: ?><span class="field-error"></span><?php endif; ?>
        </div>

        <!-- Terms -->
        <div class="form-group">
          <label class="remember-label">
            <input type="checkbox" name="agree" value="1" <?= isset($_POST['agree']) ? 'checked' : '' ?>>
            I agree to the <a href="#" style="color:#00adb5;">Terms &amp; Conditions</a>
          </label>
          <?php if (!empty($errors['agree'])): ?>
            <span class="field-error">⚠ <?= htmlspecialchars($errors['agree']) ?></span>
          <?php endif; ?>
        </div>

        <button class="auth-btn" type="submit" id="register-btn">Create Account</button>
      </form>

      <div class="auth-divider"><span>or</span></div>
      <p class="auth-text">Already have an account? <a href="login.php">Login →</a></p>
    </div>
  </div>

</div>

<script src="js/store.js"></script>
<script>
  function togglePw(id, btn) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
    btn.textContent = input.type === 'password' ? '👁' : '🙈';
  }

  function checkStrength() {
    const pw = document.getElementById('reg-password').value;
    const fill = document.getElementById('strength-fill');
    document.getElementById('strength-bar').style.display = 'block';
    let strength = 0;
    if (pw.length >= 6) strength++;
    if (/[A-Z]/.test(pw)) strength++;
    if (/[0-9]/.test(pw)) strength++;
    if (/[^a-zA-Z0-9]/.test(pw)) strength++;
    const colors = ['#e53e3e','#ff9f43','#ffd32a','#22a06b'];
    const widths  = ['25%','50%','75%','100%'];
    fill.style.background = colors[strength - 1] || '#eee';
    fill.style.width      = widths[strength - 1]  || '0%';
  }

  /* Client pre-validation */
  document.getElementById('register-form').addEventListener('submit', function(e) {
    let valid = true;
    const clear = () => document.querySelectorAll('.field-error').forEach(el => el.textContent = '');
    clear();

    const val = id => document.getElementById(id).value.trim();

    if (!val('name') || val('name').length < 2) { addErr('name', 'Enter a valid full name (min 2 chars).'); valid = false; }
    if (!/^[6-9]\d{9}$/.test(val('phone')))     { addErr('phone', 'Enter a valid 10-digit mobile number.'); valid = false; }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val('email'))) { addErr('email', 'Enter a valid email address.'); valid = false; }

    const pw = document.getElementById('reg-password').value;
    if (pw.length < 6) { addErrRaw('reg-password', 'password', 'Password must be at least 6 characters.'); valid = false; }
    else if (pw !== document.getElementById('reg-confirm').value) {
      addErrRaw('reg-confirm', 'confirm_password', 'Passwords do not match.'); valid = false;
    }
    if (!val('city'))   { addErr('city', 'City is required.'); valid = false; }
    if (!val('state'))  { addErr('state', 'State is required.'); valid = false; }
    if (!/^\d{6}$/.test(val('pin'))) { addErr('pin', 'Enter a valid 6-digit PIN.'); valid = false; }
    if (!document.querySelector('[name=agree]').checked) {
      const errEl = document.querySelector('[name=agree]').closest('.form-group').querySelector('.field-error')
        || (() => { const s = document.createElement('span'); s.className = 'field-error'; document.querySelector('[name=agree]').closest('.form-group').appendChild(s); return s; })();
      errEl.textContent = '⚠ You must agree to continue.'; valid = false;
    }

    if (!valid) { e.preventDefault(); return; }
    document.getElementById('register-btn').textContent = 'Creating Account…';
    document.getElementById('register-btn').disabled = true;
  });

  function addErr(inputId, msg) {
    const input = document.getElementById(inputId);
    if (!input) return;
    input.classList.add('input-error');
    let span = input.closest('.form-group')?.querySelector('.field-error');
    if (!span) { span = document.createElement('span'); span.className = 'field-error'; input.closest('.form-group').appendChild(span); }
    span.textContent = '⚠ ' + msg;
  }

  function addErrRaw(inputId, fieldName, msg) { addErr(inputId, msg); }
</script>
</body>
</html>
