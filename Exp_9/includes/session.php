<?php
/**
 * session.php
 * Session bootstrap + flash-message helpers.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── Flash messages ────────────────────────────────────────────────────────────

function flash_set(string $key, mixed $value): void {
    $_SESSION['flash'][$key] = $value;
}

function flash_get(string $key, mixed $default = null): mixed {
    $value = $_SESSION['flash'][$key] ?? $default;
    unset($_SESSION['flash'][$key]);
    return $value;
}

function flash_has(string $key): bool {
    return isset($_SESSION['flash'][$key]);
}

// ── Auth helpers ──────────────────────────────────────────────────────────────

function auth_user(): ?array {
    return $_SESSION['user'] ?? null;
}

function auth_check(): bool {
    return isset($_SESSION['user']);
}

function auth_login(array $user): void {
    $_SESSION['user'] = $user;
}

function auth_logout(): void {
    unset($_SESSION['user']);
    session_regenerate_id(true);
}

// ── CSRF ──────────────────────────────────────────────────────────────────────

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string {
    return '<input type="hidden" name="_csrf" value="' . htmlspecialchars(csrf_token()) . '">';
}

function csrf_verify(): bool {
    $token = $_POST['_csrf'] ?? '';
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

// ── Redirect helper ───────────────────────────────────────────────────────────

function redirect(string $url): never {
    header("Location: {$url}");
    exit;
}
