<?php
require_once 'Database.php'; // inclut la classe Database pour la connexion PDO

class Recipe {

    /* ============================================================
       Crée une nouvelle recette
       $data : tableau contenant title, description, category, difficulty, ingredients, instructions, prep_time, cook_time, servings, image
       $user_id : id de l'utilisateur qui crée la recette
    ============================================================ */
    public static function create($data, $user_id) {
        $stmt = Database::getInstance()->prepare(
            "INSERT INTO recipes 
            (title, description, category, difficulty, ingredients, instructions, prep_time, cook_time, servings, image, user_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        // s'assurer que category est toujours un tableau
        $categories = is_array($data['category']) ? $data['category'] : [$data['category']];

        // exécution avec toutes les données
        return $stmt->execute([
            $data['title'],
            $data['description'],
            implode(',', $categories), // stocke les catégories en CSV
            $data['difficulty'],
            $data['ingredients'],
            $data['instructions'],
            $data['prep_time'] ?? null,
            $data['cook_time'] ?? null,
            $data['servings'] ?? null,
            $data['image'] ?? null,
            $user_id
        ]);
    }

    /* ============================================================
       Retourne toutes les recettes avec filtres optionnels
       $filters peut contenir 'category' et 'search'
    ============================================================ */
    public static function all($filters = []) {
        $sql = "
            SELECT r.*, u.username, COUNT(f.id) as likes
            FROM recipes r
            JOIN users u ON r.user_id = u.id
            LEFT JOIN recipe_favorites f ON r.id = f.recipe_id
            WHERE 1
        ";
        $params = [];

        // filtre par catégorie
        if (!empty($filters['category'])) {
            $sql .= " AND FIND_IN_SET(?, r.category)";
            $params[] = $filters['category'];
        }

        // filtre par recherche texte
        if (!empty($filters['search'])) {
            $sql .= " AND (r.title LIKE ? OR r.description LIKE ?)";
            $params[] = "%" . $filters['search'] . "%";
            $params[] = "%" . $filters['search'] . "%";
        }

        $sql .= " GROUP BY r.id ORDER BY r.created_at DESC";

        $stmt = Database::getInstance()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ============================================================
       Récupère une recette par son ID
    ============================================================ */
    public static function find($id) {
        $stmt = Database::getInstance()->prepare(
            "SELECT r.*, u.username FROM recipes r 
             JOIN users u ON r.user_id = u.id 
             WHERE r.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* ============================================================
       Retourne toutes les catégories uniques utilisées
       Même si stockées en CSV
    ============================================================ */
    public static function categories() {
        $stmt = Database::getInstance()->query("SELECT DISTINCT category FROM recipes");
        $cats = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $parts = explode(',', $row['category']);
            foreach ($parts as $p) {
                $cats[trim($p)] = trim($p);
            }
        }
        return array_values($cats);
    }

    /* ============================================================
       Recettes les plus récentes, limite paramétrable
    ============================================================ */
    public static function latest($limit = 5) {
        $limit = (int)$limit;
        $stmt = Database::getInstance()->prepare("
            SELECT r.*, u.username, COUNT(f.id) as likes
            FROM recipes r
            JOIN users u ON r.user_id = u.id
            LEFT JOIN recipe_favorites f ON r.id = f.recipe_id
            GROUP BY r.id
            ORDER BY r.created_at DESC
            LIMIT $limit
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ============================================================
       Recettes favorites d’un utilisateur
    ============================================================ */
    public static function favorites($user_id) {
        $stmt = Database::getInstance()->prepare("
            SELECT r.*, u.username, COUNT(f2.id) as likes
            FROM recipe_favorites f
            JOIN recipes r ON f.recipe_id = r.id
            JOIN users u ON r.user_id = u.id
            LEFT JOIN recipe_favorites f2 ON r.id = f2.recipe_id
            WHERE f.user_id = ?
            GROUP BY r.id
            ORDER BY f.created_at DESC
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ============================================================
       Recettes créées par un utilisateur
    ============================================================ */
    public static function byUser($user_id) {
        $stmt = Database::getInstance()->prepare("
            SELECT r.*, u.username, COUNT(f.id) as likes
            FROM recipes r
            JOIN users u ON r.user_id = u.id
            LEFT JOIN recipe_favorites f ON r.id = f.recipe_id
            WHERE r.user_id = ?
            GROUP BY r.id
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ============================================================
       Historique des dernières vues d’un utilisateur (limite 20)
    ============================================================ */
    public static function history($user_id, $limit = 20) {
        $limit = (int)$limit;
        $stmt = Database::getInstance()->prepare("
            SELECT r.*, u.username, MAX(v.viewed_at) AS last_viewed
            FROM recipe_views v
            JOIN recipes r ON v.recipe_id = r.id
            JOIN users u ON r.user_id = u.id
            WHERE v.user_id = ?
            GROUP BY r.id
            ORDER BY last_viewed DESC
            LIMIT $limit
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ============================================================
       Met à jour une recette existante
    ============================================================ */
    public static function update($id, $data) {
        $categories = is_array($data['category']) ? $data['category'] : [$data['category']];
        $stmt = Database::getInstance()->prepare("
            UPDATE recipes SET
                title = ?,
                description = ?,
                category = ?,
                difficulty = ?,
                ingredients = ?,
                instructions = ?,
                prep_time = ?,
                cook_time = ?,
                servings = ?,
                image = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['title'],
            $data['description'],
            implode(',', $categories),
            $data['difficulty'],
            $data['ingredients'],
            $data['instructions'],
            $data['prep_time'] ?? null,
            $data['cook_time'] ?? null,
            $data['servings'] ?? null,
            $data['image'] ?? null,
            $id
        ]);
    }

    /* ============================================================
       Supprime une recette par son ID
    ============================================================ */
    public static function delete($id) {
        $stmt = Database::getInstance()->prepare("DELETE FROM recipes WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /* ============================================================
       Ajouter ou supprimer des favoris
    ============================================================ */
    public static function addFavorite($user_id, $recipe_id) {
        $stmt = Database::getInstance()->prepare("
            INSERT IGNORE INTO recipe_favorites (user_id, recipe_id) VALUES (?, ?)
        ");
        return $stmt->execute([$user_id, $recipe_id]);
    }

    public static function removeFavorite($user_id, $recipe_id) {
        $stmt = Database::getInstance()->prepare("
            DELETE FROM recipe_favorites WHERE user_id = ? AND recipe_id = ?
        ");
        return $stmt->execute([$user_id, $recipe_id]);
    }

    /* ============================================================
       Compte le nombre total de favoris d’une recette
    ============================================================ */
    public static function countFavorites($recipe_id) {
        $stmt = Database::getInstance()->prepare("
            SELECT COUNT(*) as total FROM recipe_favorites WHERE recipe_id = ?
        ");
        $stmt->execute([$recipe_id]);
        return (int)$stmt->fetchColumn();
    }

    /* ============================================================
       Recettes favorites d’un utilisateur (version détaillée)
    ============================================================ */
    public static function favoritesByUser($user_id) {
        $stmt = Database::getInstance()->prepare("
            SELECT r.*, u.username
            FROM recipe_favorites f
            JOIN recipes r ON f.recipe_id = r.id
            JOIN users u ON r.user_id = u.id
            WHERE f.user_id = ?
            ORDER BY f.created_at DESC
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ============================================================
       Recettes les plus populaires (par nombre de likes)
    ============================================================ */
    public static function popular($limit = 3) {
        $limit = (int)$limit;
        $stmt = Database::getInstance()->prepare("
            SELECT r.*, u.username, COUNT(f.id) as likes
            FROM recipes r
            JOIN users u ON r.user_id = u.id
            LEFT JOIN recipe_favorites f ON r.id = f.recipe_id
            GROUP BY r.id
            ORDER BY likes DESC
            LIMIT $limit
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}