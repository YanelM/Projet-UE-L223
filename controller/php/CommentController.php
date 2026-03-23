<?php
require_once(__DIR__ . "/../../model/php/CommentModel.php");

// Contrôleur pour gérer les commentaires
class CommentController {

    // Filtre les mots interdits dans le texte
    private function filterBadWords($text) {
        $badWords = ['connard','pute','merde','idiot','stupide'];
        foreach ($badWords as $word) {
            $text = preg_replace('/\b'.preg_quote($word,'/').'\b/i','****',$text);
        }
        return $text;
    }

    // Ajouter un commentaire
    public function add() {
        if (!isset($_SESSION['user'])) { header("Location: index.php?page=login"); exit; }

        $recipe_id = $_POST['recipe_id'] ?? 0;
        $content   = trim($_POST['content'] ?? '');
        $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;

        // Anti-spam : vérifier longueur minimale
        if (strlen($content) < 3) { header("Location: index.php?page=recipe&id=".$recipe_id); exit; }

        // Limiter longueur maximale
        $content = substr($content,0,500);

        // Filtrer les insultes
        $content = $this->filterBadWords($content);

        // Enregistrer le commentaire
        Comment::create($_SESSION['user']['id'],$recipe_id,$content,$parent_id);

        header("Location: index.php?page=recipe&id=".$recipe_id); exit;
    }
}