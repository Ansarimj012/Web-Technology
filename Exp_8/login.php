<?php
/**
 * login.php – PHP-powered login page with server-side validation.
 */
require_once __DIR__ . '/includes/session.php';

// Already logged in?
if (auth_check()) {
    redirect('index.php');
}

$errors   = flash_get('errors', []);
$old      = flash_get('old_input', []);
$success  = flash_get('success');
$errorMsg = flash_get('error');

function field_class(array $errors, string $field): string {
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
  <title>Login – Online Store</title>
  <link rel="stylesheet" href="styles/shared.css">
  <link rel="stylesheet" href="styles/auth.css">
  <style>
    .input-error { border-color: #e53e3e !important; background: #fff5f5 !important; }
    .server-success {
      background: #f0fff4; color: #22543d; border: 1px solid #9ae6b4;
      border-radius: 8px; padding: 11px 14px; font-size: 13px; margin-bottom: 16px;
    }
    .php-badge {
      display: inline-block; background: #6366f1; color: #fff;
      font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 20px;
      letter-spacing: 0.5px; margin-left: 8px; vertical-align: middle;
    }
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
    <a href="register.php">Register</a>
    <a href="products.php">Products</a>
    <a href="cart.php" class="cart-link">🛒 Cart <span id="cart-badge">0</span></a>
  </div>
</nav>

<div class="auth-page">

  <!-- LEFT PANEL -->
  <div class="auth-panel left-panel">
    <div class="auth-illustration">
      <div style="font-size:80px;margin-bottom:24px;">🛍️</div>
      <h2>Welcome Back!</h2>
      <p>Login to access your orders, wishlist, and personalised recommendations.</p>
      <ul class="perks">
        <li>✅ Track your orders</li>
        <li>✅ Save your cart</li>
        <li>✅ Exclusive member deals</li>
        <li>✅ Faster checkout</li>
      </ul>
    </div>
  </div>

  <!-- RIGHT PANEL -->
  <div class="auth-panel right-panel">
    <div class="auth-box">
      <h2>Login <span class="php-badge">PHP</span></h2>
      <p class="auth-subtitle">Enter your credentials to continue</p>

      <?php if ($success): ?>
        <div class="server-success">✅ <?= htmlspecialchars($success) ?></div>
      <?php endif; ?>

      <?php if ($errorMsg): ?>
        <div class="error-msg">❌ <?= htmlspecialchars($errorMsg) ?></div>
      <?php endif; ?>

      <form method="POST" action="php/login_handler.php" novalidate id="login-form">
        <?= csrf_field() ?>

        <!-- Email -->
        <div class="form-group">
          <label for="email">Email Address</label>
          <input
            type="email" id="email" name="email"
            placeholder="you@example.com"
            value="<?= old($old, 'email') ?>"
            class="<?= field_class($errors, 'email') ?>"
            autocomplete="email"
          >
          <?php if (!empty($errors['email'])): ?>
            <span class="field-error">⚠ <?= htmlspecialchars($errors['email']) ?></span>
          <?php endif; ?>
        </div>

        <!-- Password -->
        <div class="form-group">
          <label for="password">Password</label>
          <div class="password-wrapper">
            <input
              type="password" id="password" name="password"
              placeholder="Enter your password"
              class="<?= field_class($errors, 'password') ?>"
              autocomplete="current-password"
            >
            <button type="button" class="toggle-pw" onclick="togglePw('password', this)">👁</button>
          </div>
          <?php if (!empty($errors['password'])): ?>
            <span class="field-error">⚠ <?= htmlspecialchars($errors['password']) ?></span>
          <?php endif; ?>
        </div>

        <!-- Remember / Forgot -->
        <div class="remember-row">
          <label class="remember-label">
            <input type="checkbox" name="remember" value="1"> Remember me
          </label>
          <a href="#" class="forgot-link">Forgot password?</a>
        </div>

        <button class="auth-btn" type="submit" id="login-btn">Login</button>
      </form>

      <div class="auth-divider"><span>or</span></div>
      <p class="auth-text">Don't have an account? <a href="register.php">Create one →</a></p>
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

  /* Client-side pre-validation for UX (server is the authority) */
  document.getElementById('login-form').addEventListener('submit', function(e) {
    let valid = true;
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;

    document.querySelectorAll('.field-error').forEach(el => el.textContent = '');

    if (!email) {
      showInlineError('email', 'Email is required.'); valid = false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      showInlineError('email', 'Please enter a valid email.'); valid = false;
    }
    if (!password) {
      showInlineError('password', 'Password is required.'); valid = false;
    } else if (password.length < 6) {
      showInlineError('password', 'Password must be at least 6 characters.'); valid = false;
    }

    if (!valid) { e.preventDefault(); return; }
    document.getElementById('login-btn').textContent = 'Logging in…';
    document.getElementById('login-btn').disabled = true;
  });

  function showInlineError(fieldId, msg) {
    const input = document.getElementById(fieldId);
    input.classList.add('input-error');
    let span = input.closest('.form-group').querySelector('.field-error');
    if (!span) { span = document.createElement('span'); span.className = 'field-error'; input.closest('.form-group').appendChild(span); }
    span.textContent = '⚠ ' + msg;
  }

  document.addEventListener('keydown', e => { if (e.key === 'Enter' && e.target.tagName !== 'BUTTON') document.getElementById('login-form').requestSubmit(); });
</script>
</body>
</html>
