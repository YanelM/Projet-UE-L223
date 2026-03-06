<?php
require_once '../includes/config.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$pageTitle = "Add a Recipe — Cook n' Share";
$db = getDB();

$success = false;
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title        = trim($_POST['title'] ?? '');
    $description  = trim($_POST['description'] ?? '');
    $category     = trim($_POST['category'] ?? '');
    $difficulty   = $_POST['difficulty'] ?? 'easy';
    $ingredients  = trim($_POST['ingredients'] ?? '');
    $instructions = trim($_POST['instructions'] ?? '');
    $prep_time    = (int)($_POST['prep_time'] ?? 0);
    $cook_time    = (int)($_POST['cook_time'] ?? 0);
    $servings     = (int)($_POST['servings'] ?? 0);

    if (empty($title) || empty($ingredients) || empty($instructions)) {
        $error = "Please fill in the required fields (title, ingredients, instructions).";
    } else {
        $userId = $_SESSION['user_id'] ?? 1;

        $stmt = $db->prepare("
            INSERT INTO recipes
                (user_id, title, description, category, difficulty, ingredients, instructions, prep_time, cook_time, servings)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $userId, $title, $description, $category,
            $difficulty, $ingredients, $instructions,
            $prep_time, $cook_time, $servings
        ]);

        $newId = $db->lastInsertId();
        header("Location: recipe.php?id=$newId");
        exit;
    }
}

require_once '../includes/header.php';
?>

<div class="add-recipe-page">

    <div class="page-header">
        <h1>Share a Recipe</h1>
        <p>Fill in the details below and share your creation with the community.</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">

        <!-- Basic info -->
        <div class="form-section">
            <div class="form-section-title">📋 Basic Information</div>

            <div class="form-group">
                <label for="title">Recipe Title *</label>
                <input type="text" id="title" name="title" required
                    placeholder="e.g. Grandma's Chocolate Chip Cookies"
                    value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="description">Short Description</label>
                <textarea id="description" name="description" rows="3"
                    placeholder="A brief, enticing description of your dish…"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category" name="category">
                        <option value="">Select category…</option>
                        <?php
                        $cats = ['Breakfast','Pasta','Soup','Salad','Vegetarian','Meat','Seafood','Dessert','Other'];
                        foreach ($cats as $cat):
                            $sel = (($_POST['category'] ?? '') === $cat) ? 'selected' : '';
                        ?>
                        <option value="<?= $cat ?>" <?= $sel ?>><?= $cat ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="difficulty">Difficulty</label>
                    <select id="difficulty" name="difficulty">
                        <?php foreach (['easy' => 'Easy', 'medium' => 'Medium', 'hard' => 'Hard'] as $val => $label):
                            $sel = (($_POST['difficulty'] ?? 'easy') === $val) ? 'selected' : '';
                        ?>
                        <option value="<?= $val ?>" <?= $sel ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Times & servings -->
        <div class="form-section">
            <div class="form-section-title">⏱ Times & Servings</div>
            <div class="form-row">
                <div class="form-group">
                    <label for="prep_time">Prep Time (minutes)</label>
                    <input type="number" id="prep_time" name="prep_time" min="0"
                        value="<?= htmlspecialchars($_POST['prep_time'] ?? '') ?>" placeholder="15">
                </div>
                <div class="form-group">
                    <label for="cook_time">Cook Time (minutes)</label>
                    <input type="number" id="cook_time" name="cook_time" min="0"
                        value="<?= htmlspecialchars($_POST['cook_time'] ?? '') ?>" placeholder="30">
                </div>
                <div class="form-group">
                    <label for="servings">Servings</label>
                    <input type="number" id="servings" name="servings" min="1"
                        value="<?= htmlspecialchars($_POST['servings'] ?? '') ?>" placeholder="4">
                </div>
            </div>
        </div>

        <!-- Ingredients -->
        <div class="form-section">
            <div class="form-section-title">🛒 Ingredients *</div>
            <div class="form-group" style="margin-bottom:0">
                <label for="ingredients">One ingredient per line</label>
                <textarea id="ingredients" name="ingredients" rows="8"
                    placeholder="2 cups all-purpose flour&#10;1 tsp baking powder&#10;½ cup unsalted butter, softened&#10;…"><?= htmlspecialchars($_POST['ingredients'] ?? '') ?></textarea>
            </div>
        </div>

        <!-- Instructions -->
        <div class="form-section">
            <div class="form-section-title">📝 Instructions *</div>
            <div class="form-group" style="margin-bottom:0">
                <label for="instructions">One step per line</label>
                <textarea id="instructions" name="instructions" rows="10"
                    placeholder="Preheat oven to 180°C (350°F).&#10;Mix dry ingredients in a bowl.&#10;…"><?= htmlspecialchars($_POST['instructions'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="form-actions">
            <a href="../index.php" class="btn btn-ghost">Cancel</a>
            <button type="submit" class="btn btn-primary">Publish Recipe →</button>
        </div>

    </form>

</div>

<?php require_once '../includes/footer.php'; ?>
