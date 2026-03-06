<?php
// Database configuration

define('DB_HOST', 'localhost');
define('DB_NAME', 'cooknshare');
define('DB_USER', 'root');
define('DB_PASS', '');

// Website URL (match development server)
define('SITE_URL', 'http://localhost:3000');

function getDB() {

    static $pdo = null;

    if ($pdo === null) {

        try {

            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
                DB_USER,
                DB_PASS
            );

            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {

            die("Database connection failed: " . $e->getMessage());

        }

    }

    return $pdo;
}