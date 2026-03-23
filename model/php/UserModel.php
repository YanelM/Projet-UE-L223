<?php
require_once 'Database.php'; // inclut la classe de connexion à la base de données

class User {

    /* ============================================================
       Récupère un utilisateur via son email
       Retourne un tableau associatif ou false si non trouvé
    ============================================================ */
    public static function findByEmail($email) {
        $stmt = Database::getInstance()->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* ============================================================
       Crée un nouvel utilisateur
       Hash le mot de passe avant insertion
       Retourne true si succès
    ============================================================ */
    public static function create($username, $email, $password) {
        $hashed = password_hash($password, PASSWORD_DEFAULT); // hash du mot de passe
        $stmt = Database::getInstance()->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        return $stmt->execute([$username, $email, $hashed]);
    }

    /* ============================================================
       Vérifie les identifiants d'un utilisateur
       Retourne les données utilisateur si correct, false sinon
    ============================================================ */
    public static function verify($email, $password) {
        $user = self::findByEmail($email);               // récupère l'utilisateur
        if ($user && password_verify($password, $user['password'])) { // vérifie le mot de passe
            return $user;
        }
        return false;
    }

    /* ============================================================
       Met à jour toutes les infos d'un utilisateur
       Si avatar fourni, l'update également
    ============================================================ */
    public static function updateFull($id, $username, $email, $phone, $avatar = null) {
        if ($avatar) {
            $stmt = Database::getInstance()->prepare("
                UPDATE users SET username=?, email=?, phone=?, avatar=? WHERE id=?
            ");
            return $stmt->execute([$username, $email, $phone, $avatar, $id]);
        } else {
            $stmt = Database::getInstance()->prepare("
                UPDATE users SET username=?, email=?, phone=? WHERE id=?
            ");
            return $stmt->execute([$username, $email, $phone, $id]);
        }
    }

    /* ============================================================
       Récupère un utilisateur via son ID
       Retourne un tableau associatif
    ============================================================ */
    public static function findById($id) {
        $stmt = Database::getInstance()->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* ============================================================
       Récupère tous les utilisateurs (ID, username, avatar)
       Retourne un tableau d'associatifs
    ============================================================ */
    public static function all() {
        $stmt = Database::getInstance()->query("SELECT id, username, avatar FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ============================================================
       Vérifie si $user_id a liké $target_user_id
       Retourne true si oui, false sinon
    ============================================================ */
    public static function isLiked($user_id, $target_user_id) {
        $stmt = Database::getInstance()->prepare("
            SELECT 1 FROM user_likes WHERE user_id = ? AND target_user_id = ?
        ");
        $stmt->execute([$user_id, $target_user_id]);
        return (bool)$stmt->fetchColumn();
    }

    /* ============================================================
       Ajoute un like entre $user_id et $target_user_id
       INSERT IGNORE pour éviter les doublons
    ============================================================ */
    public static function addLike($user_id, $target_user_id) {
        $stmt = Database::getInstance()->prepare("
            INSERT IGNORE INTO user_likes (user_id, target_user_id) VALUES (?, ?)
        ");
        return $stmt->execute([$user_id, $target_user_id]);
    }

    /* ============================================================
       Supprime un like entre $user_id et $target_user_id
    ============================================================ */
    public static function removeLike($user_id, $target_user_id) {
        $stmt = Database::getInstance()->prepare("
            DELETE FROM user_likes WHERE user_id = ? AND target_user_id = ?
        ");
        return $stmt->execute([$user_id, $target_user_id]);
    }

    /* ============================================================
       Compte le nombre total de likes reçus par un utilisateur
       Retourne un entier
    ============================================================ */
    public static function totalLikes($target_user_id) {
        $stmt = Database::getInstance()->prepare("
            SELECT COUNT(*) FROM user_likes WHERE target_user_id = ?
        ");
        $stmt->execute([$target_user_id]);
        return (int)$stmt->fetchColumn();
    }
}