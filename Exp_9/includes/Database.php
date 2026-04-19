<?php
/**
 * Database.php
 * PDO singleton – one connection per request, lazy-loaded.
 *
 * HOW TO CONFIGURE
 * ─────────────────
 * Copy config.sample.php → config.php and fill in your credentials.
 * config.php is gitignored – never commit real credentials.
 */

class Database
{
    private static ?PDO $instance = null;

    // ── Default connection settings ──────────────────────────────────────────
    // Override by creating  Exp_7/config.php  with your real values.
    private static array $defaults = [
        'host'    => '127.0.0.1',
        'port'    => '3306',
        'dbname'  => 'online_store',
        'charset' => 'utf8mb4',
        'user'    => 'root',
        'pass'    => '',            // change in config.php
    ];

    /**
     * Returns the shared PDO instance (creates it once).
     */
    public static function connect(): PDO
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        // Merge optional config.php overrides
        $cfg = self::$defaults;
        $cfgFile = __DIR__ . '/../config.php';
        if (file_exists($cfgFile)) {
            $override = require $cfgFile;   // must return an array
            if (is_array($override)) {
                $cfg = array_merge($cfg, $override);
            }
        }

        $dsn = "mysql:host={$cfg['host']};port={$cfg['port']};"
             . "dbname={$cfg['dbname']};charset={$cfg['charset']}";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,   // real prepared statements
            PDO::MYSQL_ATTR_FOUND_ROWS   => true,    // UPDATE returns matched rows
        ];

        try {
            self::$instance = new PDO($dsn, $cfg['user'], $cfg['pass'], $options);
        } catch (PDOException $e) {
            // Never expose DB credentials in error messages
            error_log('DB connection failed: ' . $e->getMessage());
            die(json_encode(['error' => 'Database connection failed. Please try again later.']));
        }

        return self::$instance;
    }

    /** Shorthand alias */
    public static function pdo(): PDO
    {
        return self::connect();
    }

    /** Prevent instantiation */
    private function __construct() {}
    private function __clone()     {}
}
