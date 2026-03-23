<?php
require_once 'Database.php';

// Classe Commentaire
class Comment {

    // Crée un commentaire
    public static function create($user_id, $recipe_id, $content, $parent_id = null) {
        $stmt = Database::getInstance()->prepare("
            INSERT INTO comments (user_id, recipe_id, content, parent_id)
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([$user_id, $recipe_id, $content, $parent_id]); // exécute l'insertion
    }

    // Récupère tous les commentaires d'une recette
    public static function byRecipe($recipe_id) {
        $stmt = Database::getInstance()->prepare("
            SELECT c.*, u.username
            FROM comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.recipe_id = ?
            ORDER BY c.created_at ASC
        ");
        $stmt->execute([$recipe_id]);                      // exécute la requête
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);    // récupère tous

        // Transformer en arbre de réponses
        $tree = [];
        $refs = [];

        foreach ($comments as $c) {
            $c['replies'] = [];        // ajoute un tableau de réponses
            $refs[$c['id']] = $c;      // référence par id
        }

        foreach ($refs as $id => &$comment) {
            if ($comment['parent_id']) {
                $refs[$comment['parent_id']]['replies'][] = &$comment; // rattache à parent
            } else {
                $tree[] = &$comment; // commentaire racine
            }
        }

        return $tree; // retourne l'arbre
    }
}