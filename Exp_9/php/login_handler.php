<?php
/**
 * login_handler.php  (DB version)
 * Validates login form → authenticates against MySQL → creates session.
 */
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/Validator.php';
require_once __DIR__ . '/../includes/UserModel.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../login.php');
}

// CSRF
if (!csrf_verify()) {
    flash_set('error', 'Invalid form submission. Please try again.');
    redirect('../login.php');
}

// ── Validate fields ───────────────────────────────────────────────────────────
$v = new Validator();
$v->load(['email', 'password'])
  ->required('email',    'Email')
  ->email('email')
  ->required('password', 'Password')
  ->minLength('password', 6, 'Password');

if ($v->fails()) {
    flash_set('errors',    $v->errors());
    flash_set('old_input', ['email' => $v->get('email')]);
    redirect('../login.php');
}

$email    = $v->get('email');
$password = $_POST['password'];          // raw for password_verify()
$ip       = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

$userModel = new UserModel();

// ── Brute-force guard (max 10 attempts / 15 min) ──────────────────────────────
if ($userModel->recentFailedAttempts($email, $ip) >= 10) {
    flash_set('error', 'Too many failed login attempts. Please wait 15 minutes and try again.');
    flash_set('old_input', ['email' => $email]);
    redirect('../login.php');
}

// ── Authenticate ──────────────────────────────────────────────────────────────
$user = $userModel->findByEmail($email);
$authenticated = $user && (int)$user['is_active'] === 1
              && password_verify($password, $user['password_hash']);

if (!$authenticated) {
    $userModel->logLoginAttempt($email, $ip);
    flash_set('error', 'Invalid email or password.');
    flash_set('old_input', ['email' => $email]);
    redirect('../login.php');
}

// ── Session ───────────────────────────────────────────────────────────────────
session_regenerate_id(true);
auth_login([
    'id'    => (int) $user['id'],
    'name'  => $user['name'],
    'email' => $user['email'],
]);

// ── Remember Me (7-day cookie, token stored hashed in DB) ────────────────────
if (!empty($_POST['remember'])) {
    $rawToken    = bin2hex(random_bytes(32));
    $hashedToken = hash('sha256', $rawToken);
    $userModel->setRememberToken((int)$user['id'], $hashedToken);
    setcookie('remember_token', $rawToken, [
        'expires'  => time() + 604800,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

// Re-hash password if bcrypt cost has changed
if (password_needs_rehash($user['password_hash'], PASSWORD_BCRYPT, ['cost' => 12])) {
    $newHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    Database::pdo()
        ->prepare('UPDATE users SET password_hash = ? WHERE id = ?')
        ->execute([$newHash, $user['id']]);
}

flash_set('success', "Welcome back, {$user['name']}! 👋");
redirect('../index.php');
