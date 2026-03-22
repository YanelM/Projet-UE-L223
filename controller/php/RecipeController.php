<?php
require_once(__DIR__ . "/../../model/php/RecipeModel.php");
class RecipeController {

    public function list() {
        $selectedCat = $_GET['category'] ?? '';
        $search      = trim($_GET['search'] ?? '');

        $filters = [
            'category' => $selectedCat,
            'search'   => $search,
        ];

        // Récupérer les recettes filtrées
        $recipes = Recipe::all($filters);

        // Récupérer les catégories existantes
        $categories = Recipe::categories();

        // Préparer les icônes pour chaque recette
        $categoryIcons = [
            'Pasta'       => '🍝',
            'Vegetarian'  => '🥗',
            'Dessert'     => '🍰',
            'Soup'        => '🍲',
            'Seafood'     => '🦞',
            'Meat'        => '🥩',
            'Breakfast'   => '🥞',
            'Salad'       => '🥙',
        ];

        foreach ($recipes as &$r) {
            $r['icon'] = $categoryIcons[$r['category']] ?? '🍽️';
            $r['totalTime'] = ($r['prep_time'] ?? 0) + ($r['cook_time'] ?? 0);
        }

        include(__DIR__ . "/../../view/php/recipes.php");
    }

    public function view() {
        $id = $_GET['id'] ?? 0;
        $recipe = Recipe::find($id);

        // Rediriger si la recette n'existe pas
        if (!$recipe) {
            header("Location: index.php?page=recipes");
            exit;
        }

        // Historique
        if (isset($_SESSION['user'])) {
            $stmt = Database::getInstance()->prepare("
                INSERT INTO recipe_views (user_id, recipe_id) VALUES (?, ?)
            ");
            $stmt->execute([$_SESSION['user']['id'], $id]);
        }

        // 🔥 AJOUT : charger les commentaires
        require_once(__DIR__ . "/../../model/php/CommentModel.php");
        $comments = Comment::byRecipe($id);

        // UI data
        $categoryIcons = [
            'Pasta' => '🍝', 'Vegetarian' => '🥗', 'Dessert' => '🍰',
            'Soup'  => '🍲', 'Seafood' => '🦞', 'Meat' => '🥩',
            'Breakfast' => '🥞', 'Salad' => '🥙',
        ];

        $icon = $categoryIcons[$recipe['category']] ?? '🍽️';
        $totalTime = ($recipe['prep_time'] ?? 0) + ($recipe['cook_time'] ?? 0);
        $ingredients  = array_filter(array_map('trim', explode("\n", $recipe['ingredients'] ?? '')));
        $instructions = array_filter(array_map('trim', explode("\n", $recipe['instructions'] ?? '')));

        include(__DIR__ . "/../../view/php/recipe.php");
    }

    public function add() {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?page=login");
            exit;
        }

        $error = '';
        $success = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title        = trim($_POST['title'] ?? '');
            $description  = trim($_POST['description'] ?? '');
            $categories   = $_POST['category'] ?? [];
            $difficulty   = $_POST['difficulty'] ?? 'easy';
            $prep_time    = (int)($_POST['prep_time'] ?? 0);
            $servings     = (int)($_POST['servings'] ?? 0);

            // 🔹 Gestion des ingrédients et instructions dynamiques
            $ingredients  = isset($_POST['ingredients']) && is_array($_POST['ingredients'])
                ? implode("\n", array_map('trim', $_POST['ingredients']))
                : '';
            $instructions = isset($_POST['instructions']) && is_array($_POST['instructions'])
                ? implode("\n", array_map('trim', $_POST['instructions']))
                : '';

            // Validation des champs obligatoires
            if (empty($title) || empty($ingredients) || empty($instructions)) {
                $error = "Please fill in the required fields (title, ingredients, instructions).";
            }

            // Validation des catégories
            $allowed_cats = [
                'Breakfast','Brunch','Appetizer','Soup','Salad','Pasta','Rice & Grains',
                'Vegetarian','Vegan','Meat','Poultry','Seafood','Dessert',
                'Snack','Side dish','Sauce','Beverage','Other'
            ];

            foreach ($categories as $cat) {
                if ($cat !== '' && !in_array($cat, $allowed_cats)) {
                    $error = "One of the selected categories is invalid.";
                    break;
                }
            }

            // 🔹 Gestion de l'image
            $imagePath = null;
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

            if (!empty($_FILES['image']['name'])) {
                if (in_array($_FILES['image']['type'], $allowedTypes)) {
                    $uploadDir = 'uploads/recipes/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

                    $filename = uniqid() . '_' . basename($_FILES['image']['name']);
                    $target = $uploadDir . $filename;

                    move_uploaded_file($_FILES['image']['tmp_name'], $target);
                    $imagePath = $target;
                }
            }

            if (!$error) {
                // Crée la recette via le modèle
                Recipe::create([
                    'image'        => $imagePath,
                    'title'        => $title,
                    'description'  => $description,
                    'category'     => $categories,
                    'difficulty'   => $difficulty,
                    'ingredients'  => $ingredients,
                    'instructions' => $instructions,
                    'prep_time'    => $prep_time,
                    'servings'     => $servings
                ], $_SESSION['user']['id']);

                // Redirection pour éviter le double POST
                header("Location: index.php?page=recipes");
                exit;
            }
        }

        // Préparer les catégories pour le formulaire
        $cats = [
            'Breakfast','Brunch','Appetizer','Soup','Salad','Pasta','Rice & Grains',
            'Vegetarian','Vegan','Meat','Poultry','Seafood','Dessert',
            'Snack','Side dish','Sauce','Beverage',
            'Gluten-Free','Nut-Free','Dairy-Free','Egg-Free','Soy-Free','Shellfish-Free',
            'Low Sugar','Low Carb','High Protein','Spicy','Kid-Friendly','Quick & Easy',
            'Organic','Fermented','Raw','Whole Grain','Comfort Food','Street Food',
            'BBQ','Grilled','Baked','Fried','Steamed','Slow-Cooked','Roasted',
            'Smoothie','Juice','Tea','Coffee','Cocktail','Mocktail','Water','Milkshake',
            'Breakfast Sandwich','Bagel','Pancakes','Waffles','Omelette','Cereal','Granola',
            'Soup & Stew','Salad Bowl','Pasta Dish','Risotto','Curry','Stir-Fry','Pizza',
            'Burger','Sandwich','Wrap','Taco','Sushi','Seafood Platter','Charcuterie',
            'Dessert Cake','Ice Cream','Cookie','Brownie','Pie','Pudding','Chocolate',
            'Vegan Dessert','Fruit Salad','Energy Bar','Trail Mix','Chips','Popcorn',
            'Vegetable Side','Potato Side','Rice Side','Bread & Roll','Sauce & Dressing',
            'Condiment','Dip','Smoothie Bowl','Herbal Tea','Iced Tea','Soft Drink','Beer','Wine','Other'
        ];

        include(__DIR__ . "/../../view/php/add_recipe.php");
    }

    public function edit() {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?page=login");
            exit;
        }

        $id = $_GET['id'] ?? 0;
        $recipe = Recipe::find($id);

        // Vérifier que la recette existe et appartient à l'utilisateur
        if (!$recipe || $recipe['user_id'] != $_SESSION['user']['id']) {
            header("Location: index.php?page=profile&view=my_recipes");
            exit;
        }

        $error = '';
        $imagePath = $recipe['image']; // garder l'ancienne image par défaut

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title       = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $categories  = $_POST['category'] ?? [];
            $difficulty  = $_POST['difficulty'] ?? 'easy';

            // Ingrédients et instructions sous forme de tableau
            $ingredientsArray  = $_POST['ingredients'] ?? [];
            $instructionsArray = $_POST['instructions'] ?? [];

            // Nettoyer chaque élément
            $ingredients  = array_map('trim', $ingredientsArray);
            $instructions = array_map('trim', $instructionsArray);

            // Convertir en texte avec retour à la ligne
            $ingredientsStr  = implode("\n", $ingredients);
            $instructionsStr = implode("\n", $instructions);

            $prep_time = (int)($_POST['prep_time'] ?? 0);
            $cook_time = (int)($_POST['cook_time'] ?? 0);
            $servings  = (int)($_POST['servings'] ?? 0);

            // Vérification des champs obligatoires
            if (empty($title) || empty($ingredientsStr) || empty($instructionsStr)) {
                $error = "Please fill in the required fields.";
            }

            // Gestion upload image
            if (!empty($_FILES['image']['name'])) {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

                if (in_array($_FILES['image']['type'], $allowedTypes)) {
                    $uploadDir = 'uploads/recipes/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

                    $filename = uniqid() . '_' . basename($_FILES['image']['name']);
                    $target = $uploadDir . $filename;

                    move_uploaded_file($_FILES['image']['tmp_name'], $target);

                    $imagePath = $target;
                }
            }

            if (!empty($_POST['delete_image'])) {
                $imagePath = null;
            }

            if (!$error) {
                Recipe::update($id, [
                    'image'        => $imagePath,
                    'title'        => $title,
                    'description'  => $description,
                    'category'     => implode(',', $categories), // ou JSON si tu veux
                    'difficulty'   => $difficulty,
                    'ingredients'  => $ingredientsStr,
                    'instructions' => $instructionsStr,
                    'prep_time'    => $prep_time,
                    'cook_time'    => $cook_time,
                    'servings'     => $servings
                ]);

                // Redirection vers la page recette après modification
                header("Location: index.php?page=recipe&id=$id&success=1");
                exit;
            }
        }

        // Liste des catégories possibles
        $cats = [
            'Breakfast','Brunch','Appetizer','Soup','Salad','Pasta','Rice & Grains',
            'Vegetarian','Vegan','Meat','Poultry','Seafood','Dessert',
            'Snack','Side dish','Sauce','Beverage',
            'Gluten-Free','Nut-Free','Dairy-Free','Egg-Free','Soy-Free','Shellfish-Free',
            'Low Sugar','Low Carb','High Protein','Spicy','Kid-Friendly','Quick & Easy',
            'Organic','Fermented','Raw','Whole Grain','Comfort Food','Street Food',
            'BBQ','Grilled','Baked','Fried','Steamed','Slow-Cooked','Roasted',
            'Smoothie','Juice','Tea','Coffee','Cocktail','Mocktail','Water','Milkshake',
            'Breakfast Sandwich','Bagel','Pancakes','Waffles','Omelette','Cereal','Granola',
            'Soup & Stew','Salad Bowl','Pasta Dish','Risotto','Curry','Stir-Fry','Pizza',
            'Burger','Sandwich','Wrap','Taco','Sushi','Seafood Platter','Charcuterie',
            'Dessert Cake','Ice Cream','Cookie','Brownie','Pie','Pudding','Chocolate',
            'Vegan Dessert','Fruit Salad','Energy Bar','Trail Mix','Chips','Popcorn',
            'Vegetable Side','Potato Side','Rice Side','Bread & Roll','Sauce & Dressing',
            'Condiment','Dip','Smoothie Bowl','Herbal Tea','Iced Tea','Soft Drink','Beer','Wine','Other'
        ];

        include(__DIR__ . "/../../view/php/edit_recipe.php");
    }

    public function delete() {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?page=login");
            exit;
        }

        $id = $_GET['id'] ?? 0;
        $recipe = Recipe::find($id);

        // Vérifier que la recette existe et appartient à l'utilisateur
        if (!$recipe || $recipe['user_id'] != $_SESSION['user']['id']) {
            header("Location: index.php?page=profile&view=my_recipes");
            exit;
        }

        try {
            Recipe::delete($id);
            $_SESSION['success'] = "Recipe deleted successfully.";
            // suppression réussie → renvoyer au profil
            header("Location: index.php?page=profile&view=my_recipes");
            exit;

        } catch (PDOException $e) {
            // Vérifier si c'est une contrainte de clé étrangère (recette en favoris)
            if ($e->getCode() == 23000) {
                $_SESSION['error'] = "You cannot delete this recipe because it is in someone's favorites.";
                // rester sur la page de la recette
                header("Location: index.php?page=recipe&id=$id");
                exit;
            } else {
                throw $e;
            }
        }
    }

    public function toggleFavorite() {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?page=login");
            exit;
        }

        $recipe_id = $_GET['id'] ?? 0;
        $user_id = $_SESSION['user']['id'];

        // Vérifier si l'utilisateur a déjà mis la recette en favori
        $stmt = Database::getInstance()->prepare("
            SELECT * FROM recipe_favorites WHERE user_id = ? AND recipe_id = ?
        ");
        $stmt->execute([$user_id, $recipe_id]);
        $exists = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($exists) {
            Recipe::removeFavorite($user_id, $recipe_id);
        } else {
            Recipe::addFavorite($user_id, $recipe_id);
        }

        header("Location: index.php?page=recipe&id=$recipe_id");
        exit;
    }
}