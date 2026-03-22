<?php
// Database configuration OVH
define('DB_HOST', 'localhost'); define('DB_NAME', 'cooknshare'); define('DB_USER', 'root'); define('DB_PASS', '');

// Website URL (URL de ton vrai site en ligne)
define('SITE_URL', 'http://localhost:223/Projet-UE-L223/'); 

// Le reste de ta classe Database ne change pas...
class Database {
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct() {
        try {
            $this->pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->pdo;
    }
}
