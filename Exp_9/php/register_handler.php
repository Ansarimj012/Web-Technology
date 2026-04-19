<?php
/**
 * register_handler.php  (DB version)
 * Validates registration form → inserts user into MySQL → auto-login.
 */
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/Validator.php';
require_once __DIR__ . '/../includes/UserModel.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../register.php');
}

if (!csrf_verify()) {
    flash_set('error', 'Invalid form submission. Please try again.');
    redirect('../register.php');
}

// ── Validate ──────────────────────────────────────────────────────────────────
$fields = ['name','phone','email','password','confirm_password','city','state','pin'];
$v = new Validator();
$v->load($fields)
  ->required('name',             'Full Name')
  ->minLength('name', 2,         'Full Name')
  ->maxLength('name', 100,       'Full Name')
  ->onlyAlpha('name',            'Full Name')
  ->required('phone',            'Phone Number')
  ->phone('phone',               'Phone Number')
  ->required('email',            'Email')
  ->email('email')
  ->maxLength('email', 255,      'Email')
  ->required('password',         'Password')
  ->minLength('password', 6,     'Password')
  ->maxLength('password', 72,    'Password')
  ->required('confirm_password', 'Confirm Password')
  ->matches('confirm_password', 'password', 'Passwords')
  ->required('city',             'City')
  ->maxLength('city',  60,       'City')
  ->required('state',            'State')
  ->maxLength('state', 60,       'State')
  ->required('pin',              'PIN Code')
  ->pincode('pin')
  ->checkbox('agree',            'the Terms & Conditions');

if ($v->fails()) {
    flash_set('errors', $v->errors());
    $old = $v->all();
    unset($old['password'], $old['confirm_password']);
    flash_set('old_input', $old);
    redirect('../register.php');
}

// ── Duplicate email check (DB) ────────────────────────────────────────────────
$userModel = new UserModel();

if ($userModel->findByEmail($v->get('email'))) {
    flash_set('errors', ['email' => 'This email address is already registered.']);
    $old = $v->all();
    unset($old['password'], $old['confirm_password']);
    flash_set('old_input', $old);
    redirect('../register.php');
}

// ── Insert into DB ────────────────────────────────────────────────────────────
try {
    $newUserId = $userModel->create([
        'name'          => $v->get('name'),
        'email'         => $v->get('email'),
        'phone'         => $v->get('phone'),
        'city'          => $v->get('city'),
        'state'         => $v->get('state'),
        'pin'           => $v->get('pin'),
        // NEVER store plain text – bcrypt with cost 12
        'password_hash' => password_hash($_POST['password'], PASSWORD_BCRYPT, ['cost' => 12]),
    ]);
} catch (Throwable $e) {
    error_log('Registration DB error: ' . $e->getMessage());
    flash_set('error', 'Registration failed. Please try again.');
    redirect('../register.php');
}

// ── Auto-login ────────────────────────────────────────────────────────────────
session_regenerate_id(true);
auth_login([
    'id'    => $newUserId,
    'name'  => $v->get('name'),
    'email' => $v->get('email'),
]);

flash_set('success', "Account created! Welcome, {$v->get('name')}! 🎉");
redirect('../index.php');
