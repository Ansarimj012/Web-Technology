<?php
$hostname = "sql107.infinityfree.com";
$username = "if0_41705056";
$password = "d9c5aovCBq0";
$dbname   = "if0_41705056_chat";

$conn = mysqli_connect($hostname, $username, $password, $dbname);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

// AES-256 encryption key (store this safely, e.g., in .env in production)
define('MSG_ENCRYPT_KEY', 'a8f3b2c9d4e5f6a7b8c9d0e1f2a3b4c5');
define('MSG_ENCRYPT_IV',  '1234567890abcdef');

function encrypt_message($plaintext) {
    return base64_encode(openssl_encrypt($plaintext, 'AES-256-CBC', MSG_ENCRYPT_KEY, 0, MSG_ENCRYPT_IV));
}

function decrypt_message($ciphertext) {
    $decoded = base64_decode($ciphertext);
    if ($decoded === false) return $ciphertext; // fallback for old plain messages
    $result = openssl_decrypt($decoded, 'AES-256-CBC', MSG_ENCRYPT_KEY, 0, MSG_ENCRYPT_IV);
    return $result !== false ? $result : $ciphertext;
}
?>
