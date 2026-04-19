<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/UserModel.php';

$user = auth_user();

// Clear remember-me token from DB
if ($user && isset($user['id'])) {
    (new UserModel())->clearRememberToken((int)$user['id']);
}

// Clear remember-me cookie
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', ['expires' => time() - 3600, 'path' => '/', 'httponly' => true]);
}

auth_logout();
flash_set('success', 'Logged out successfully.');
redirect('../index.php');
