<?php
require_once 'Database.php';

class User {
    public static function findByEmail($email) {
        $stmt = Database::getInstance()->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($username, $email, $password) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = Database::getInstance()->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        return $stmt->execute([$username, $email, $hashed]);
    }

    public static function verify($email, $password) {
        $user = self::findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

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

    public static function findById($id) {
        $stmt = Database::getInstance()->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function all() {
        $stmt = Database::getInstance()->query("SELECT id, username, avatar FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Vérifie si $user_id a liké $target_user_id
    public static function isLiked($user_id, $target_user_id) {
        $stmt = Database::getInstance()->prepare("
            SELECT 1 FROM user_likes WHERE user_id = ? AND target_user_id = ?
        ");
        $stmt->execute([$user_id, $target_user_id]);
        return (bool)$stmt->fetchColumn();
    }

    // Ajouter un like
    public static function addLike($user_id, $target_user_id) {
        $stmt = Database::getInstance()->prepare("
            INSERT IGNORE INTO user_likes (user_id, target_user_id) VALUES (?, ?)
        ");
        return $stmt->execute([$user_id, $target_user_id]);
    }

    // Retirer un like
    public static function removeLike($user_id, $target_user_id) {
        $stmt = Database::getInstance()->prepare("
            DELETE FROM user_likes WHERE user_id = ? AND target_user_id = ?
        ");
        return $stmt->execute([$user_id, $target_user_id]);
    }

    // Compte total de likes reçus par un utilisateur
    public static function totalLikes($target_user_id) {
        $stmt = Database::getInstance()->prepare("
            SELECT COUNT(*) FROM user_likes WHERE target_user_id = ?
        ");
        $stmt->execute([$target_user_id]);
        return (int)$stmt->fetchColumn();
    }
}