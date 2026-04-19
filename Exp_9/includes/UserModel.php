<?php
/**
 * UserModel.php
 * All database operations related to users.
 */
require_once __DIR__ . '/Database.php';

class UserModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::pdo();
    }

    // ── Finders ──────────────────────────────────────────────────────────────

    /**
     * Find a user by email (returns array or null).
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, name, email, phone, password_hash, city, state, pin, is_active
               FROM users
              WHERE email = :email
              LIMIT 1'
        );
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Find a user by primary key.
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, name, email, phone, city, state, pin, created_at
               FROM users
              WHERE id = :id
              LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    // ── Write operations ──────────────────────────────────────────────────────

    /**
     * Insert a new user. Returns the new user's ID.
     * Password must already be hashed with password_hash().
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (name, email, phone, password_hash, city, state, pin)
             VALUES (:name, :email, :phone, :password_hash, :city, :state, :pin)'
        );
        $stmt->execute([
            ':name'          => $data['name'],
            ':email'         => $data['email'],
            ':phone'         => $data['phone'],
            ':password_hash' => $data['password_hash'],
            ':city'          => $data['city']  ?? '',
            ':state'         => $data['state'] ?? '',
            ':pin'           => $data['pin']   ?? '',
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Update remember-me token (stored as sha256 hash).
     */
    public function setRememberToken(int $userId, string $hashedToken): void
    {
        $stmt = $this->db->prepare(
            'UPDATE users SET remember_token = :token WHERE id = :id'
        );
        $stmt->execute([':token' => $hashedToken, ':id' => $userId]);
    }

    /**
     * Find user by remember-me token.
     */
    public function findByRememberToken(string $hashedToken): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, name, email FROM users WHERE remember_token = :token LIMIT 1'
        );
        $stmt->execute([':token' => $hashedToken]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Clear remember token on logout.
     */
    public function clearRememberToken(int $userId): void
    {
        $stmt = $this->db->prepare(
            'UPDATE users SET remember_token = NULL WHERE id = :id'
        );
        $stmt->execute([':id' => $userId]);
    }

    // ── Brute-force protection ────────────────────────────────────────────────

    /**
     * Log a failed login attempt.
     */
    public function logLoginAttempt(string $email, string $ip): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO login_attempts (email, ip_address) VALUES (:email, :ip)'
        );
        $stmt->execute([':email' => $email, ':ip' => $ip]);
    }

    /**
     * Count failed attempts in last $minutes minutes for email OR ip.
     */
    public function recentFailedAttempts(string $email, string $ip, int $minutes = 15): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM login_attempts
              WHERE (email = :email OR ip_address = :ip)
                AND attempted_at >= DATE_SUB(NOW(), INTERVAL :mins MINUTE)'
        );
        $stmt->execute([':email' => $email, ':ip' => $ip, ':mins' => $minutes]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Purge old attempts (call occasionally to keep table small).
     */
    public function purgeOldAttempts(int $hoursOld = 24): void
    {
        $stmt = $this->db->prepare(
            'DELETE FROM login_attempts
              WHERE attempted_at < DATE_SUB(NOW(), INTERVAL :h HOUR)'
        );
        $stmt->execute([':h' => $hoursOld]);
    }
}
