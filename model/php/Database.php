<?php
// Config DB
define('DB_HOST', 'localhost');   // serveur
define('DB_NAME', 'cooknshare');  // nom base
define('DB_USER', 'root');        // utilisateur
define('DB_PASS', '');            // mot de passe

// URL du site
define('SITE_URL', 'http://localhost:223/Projet-UE-L223/'); 

// Classe singleton pour PDO
class Database {
    private static ?Database $instance = null; // instance unique
    private PDO $pdo;                           // objet PDO

    // Constructeur privé pour éviter plusieurs instances
    private function __construct() {
        try {
            $this->pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // erreurs en exception
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage()); // stop si erreur
        }
    }

    // Retourne l'instance PDO
    public static function getInstance(): PDO {
        if (self::$instance === null) {          // si pas encore créée
            self::$instance = new self();        // crée l'instance
        }
        return self::$instance->pdo;             // retourne PDO
    }
}