<?php
require_once(__DIR__ . "/../../model/php/RecipeModel.php");

// Contrôleur pour gérer les recettes
class RecipeController {

    // Liste toutes les recettes avec filtres
    public function list() {
        $selectedCat = $_GET['category'] ?? '';
        $search      = trim($_GET['search'] ?? '');

        $filters = ['category'=>$selectedCat, 'search'=>$search];

        $recipes = Recipe::all($filters);      // récupère recettes filtrées
        $categories = Recipe::categories();    // récupère toutes catégories

        // icônes par catégorie
        $categoryIcons = ['Pasta'=>'🍝','Vegetarian'=>'🥗','Dessert'=>'🍰','Soup'=>'🍲','Seafood'=>'🦞','Meat'=>'🥩','Breakfast'=>'🥞','Salad'=>'🥙'];

        foreach ($recipes as &$r) {
            $r['icon'] = $categoryIcons[$r['category']] ?? '🍽️';
            $r['totalTime'] = ($r['prep_time'] ?? 0) + ($r['cook_time'] ?? 0);
        }

        include(__DIR__ . "/../../view/php/recipes.php"); // affiche page recettes
    }

    // Affiche une recette unique
    public function view() {
        $id = $_GET['id'] ?? 0;
        $recipe = Recipe::find($id);  // récupère la recette

        if (!$recipe) { header("Location: index.php?page=recipes"); exit; }

        // historique des vues si connecté
        if (isset($_SESSION['user'])) {
            $stmt = Database::getInstance()->prepare("INSERT INTO recipe_views (user_id, recipe_id) VALUES (?, ?)");
            $stmt->execute([$_SESSION['user']['id'], $id]);
        }

        // charger commentaires
        require_once(__DIR__ . "/../../model/php/CommentModel.php");
        $comments = Comment::byRecipe($id);

        // données UI
        $categoryIcons = ['Pasta'=>'🍝','Vegetarian'=>'🥗','Dessert'=>'🍰','Soup'=>'🍲','Seafood'=>'🦞','Meat'=>'🥩','Breakfast'=>'🥞','Salad'=>'🥙'];
        $icon = $categoryIcons[$recipe['category']] ?? '🍽️';
        $totalTime = ($recipe['prep_time'] ?? 0) + ($recipe['cook_time'] ?? 0);
        $ingredients  = array_filter(array_map('trim', explode("\n", $recipe['ingredients'] ?? '')));
        $instructions = array_filter(array_map('trim', explode("\n", $recipe['instructions'] ?? '')));

        include(__DIR__ . "/../../view/php/recipe.php"); // affiche recette
    }

    // Ajouter une nouvelle recette
    public function add() {
        if (!isset($_SESSION['user'])) { header("Location: index.php?page=login"); exit; }

        $error = ''; $success = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $categories = $_POST['category'] ?? [];
            $difficulty = $_POST['difficulty'] ?? 'easy';
            $prep_time = (int)($_POST['prep_time'] ?? 0);
            $servings = (int)($_POST['servings'] ?? 0);

            // ingrédients et instructions
            $ingredients = isset($_POST['ingredients']) ? implode("\n", array_map('trim', $_POST['ingredients'])) : '';
            $instructions = isset($_POST['instructions']) ? implode("\n", array_map('trim', $_POST['instructions'])) : '';

            if (empty($title) || empty($ingredients) || empty($instructions))
                $error = "Please fill in the required fields (title, ingredients, instructions).";

            // validation catégories
            $allowed_cats = ['Breakfast','Brunch','Appetizer','Soup','Salad','Pasta','Rice & Grains','Vegetarian','Vegan','Meat','Poultry','Seafood','Dessert','Snack','Side dish','Sauce','Beverage','Other'];
            foreach ($categories as $cat) if ($cat!=='' && !in_array($cat,$allowed_cats)) { $error="Invalid category"; break; }

            // upload image
            $imagePath = null; $allowedTypes=['image/jpeg','image/png','image/webp'];
            if (!empty($_FILES['image']['name']) && in_array($_FILES['image']['type'],$allowedTypes)) {
                $uploadDir='uploads/recipes/'; if(!is_dir($uploadDir)) mkdir($uploadDir,0777,true);
                $filename=uniqid().'_'.basename($_FILES['image']['name']);
                move_uploaded_file($_FILES['image']['tmp_name'],$uploadDir.$filename);
                $imagePath=$uploadDir.$filename;
            }

            if (!$error) {
                Recipe::create([
                    'image'=>$imagePath,'title'=>$title,'description'=>$description,'category'=>$categories,
                    'difficulty'=>$difficulty,'ingredients'=>$ingredients,'instructions'=>$instructions,
                    'prep_time'=>$prep_time,'servings'=>$servings
                ],$_SESSION['user']['id']);
                header("Location: index.php?page=recipes"); exit;
            }
        }

        // catégories pour le formulaire
        $cats = ['Breakfast','Brunch','Appetizer','Soup','Salad','Pasta','Rice & Grains','Vegetarian','Vegan','Meat','Poultry','Seafood','Dessert','Snack','Side dish','Sauce','Beverage','Other'];
        include(__DIR__ . "/../../view/php/add_recipe.php"); // affiche form
    }

    // Modifier recette existante
    public function edit() {
        if (!isset($_SESSION['user'])) { header("Location: index.php?page=login"); exit; }

        $id = $_GET['id'] ?? 0;
        $recipe = Recipe::find($id);
        if (!$recipe || $recipe['user_id'] != $_SESSION['user']['id'])
            header("Location: index.php?page=profile&view=my_recipes");

        $error = ''; $imagePath = $recipe['image'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $categories = $_POST['category'] ?? [];
            $difficulty = $_POST['difficulty'] ?? 'easy';

            $ingredients = implode("\n", array_map('trim', $_POST['ingredients'] ?? []));
            $instructions = implode("\n", array_map('trim', $_POST['instructions'] ?? []));

            $prep_time = (int)($_POST['prep_time'] ?? 0);
            $cook_time = (int)($_POST['cook_time'] ?? 0);
            $servings = (int)($_POST['servings'] ?? 0);

            if (empty($title) || empty($ingredients) || empty($instructions))
                $error = "Please fill in the required fields.";

            // upload nouvelle image
            if (!empty($_FILES['image']['name'])) {
                $allowedTypes = ['image/jpeg','image/png','image/webp'];
                if (in_array($_FILES['image']['type'],$allowedTypes)) {
                    $uploadDir='uploads/recipes/'; if(!is_dir($uploadDir)) mkdir($uploadDir,0777,true);
                    $filename=uniqid().'_'.basename($_FILES['image']['name']);
                    move_uploaded_file($_FILES['image']['tmp_name'],$uploadDir.$filename);
                    $imagePath=$uploadDir.$filename;
                }
            }
            if (!empty($_POST['delete_image'])) $imagePath=null;

            if (!$error) {
                Recipe::update($id,[
                    'image'=>$imagePath,'title'=>$title,'description'=>$description,
                    'category'=>implode(',',$categories),'difficulty'=>$difficulty,
                    'ingredients'=>$ingredients,'instructions'=>$instructions,
                    'prep_time'=>$prep_time,'cook_time'=>$cook_time,'servings'=>$servings
                ]);
                header("Location: index.php?page=recipe&id=$id&success=1"); exit;
            }
        }

        $cats = ['Breakfast','Brunch','Appetizer','Soup','Salad','Pasta','Rice & Grains','Vegetarian','Vegan','Meat','Poultry','Seafood','Dessert','Snack','Side dish','Sauce','Beverage','Other'];
        include(__DIR__ . "/../../view/php/edit_recipe.php"); // affiche form
    }

    // Supprimer une recette
    public function delete() {
        if (!isset($_SESSION['user'])) { header("Location: index.php?page=login"); exit; }

        $id = $_GET['id'] ?? 0;
        $recipe = Recipe::find($id);
        if (!$recipe || $recipe['user_id'] != $_SESSION['user']['id'])
            header("Location: index.php?page=profile&view=my_recipes");

        // vérifier si recette en favoris ou vue
        $stmt = Database::getInstance()->prepare("SELECT COUNT(*) FROM recipe_favorites WHERE recipe_id=?"); $stmt->execute([$id]);
        $favoritesCount = (int)$stmt->fetchColumn();
        $stmt2 = Database::getInstance()->prepare("SELECT COUNT(*) FROM recipe_views WHERE recipe_id=?"); $stmt2->execute([$id]);
        $viewsCount = (int)$stmt2->fetchColumn();

        if ($favoritesCount>0 || $viewsCount>0) {
            $_SESSION['error']="Cannot delete, recipe has favorites/views.";
            header("Location: index.php?page=recipe&id=$id"); exit;
        }

        Recipe::delete($id); // supprime recette
        header("Location: index.php?page=profile&view=my_recipes"); exit;
    }

    // Ajouter ou retirer une recette des favoris
    public function toggleFavorite() {
        if (!isset($_SESSION['user'])) { header("Location: index.php?page=login"); exit; }

        $recipe_id = $_GET['id'] ?? 0;
        $user_id = $_SESSION['user']['id'];

        $stmt = Database::getInstance()->prepare("SELECT * FROM recipe_favorites WHERE user_id=? AND recipe_id=?");
        $stmt->execute([$user_id,$recipe_id]);
        $exists = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($exists) Recipe::removeFavorite($user_id,$recipe_id);
        else Recipe::addFavorite($user_id,$recipe_id);

        header("Location: index.php?page=recipe&id=$recipe_id"); exit;
    }
}