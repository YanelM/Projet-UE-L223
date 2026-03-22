<?php
$pageTitle = "Add Recipe — Cook n' Share";
require_once 'header.php';
?>
<div class="add-recipe-page">

    <div class="page-header">
        <h1>Share a Recipe</h1>
        <p>Fill in the details below and share your creation with the community.</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= SITE_URL ?>/index.php?page=add_recipe" enctype="multipart/form-data">
        
        <!-- Basic info -->
        <div class="form-section">
            <div class="form-section-title">📸 Recipe Image</div>
            <label for="image">Upload a photo</label>
            <input type="file" id="image" name="image" accept="image/*">
        </div>
        
        <div class="form-section">
            <div class="form-section-title">📋 Basic Information</div>
            <div class="form-group">
                <label for="title">Recipe Title *</label>
                <input type="text" id="title" name="title" required
                       value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="description">Short Description</label>
                <textarea id="description" name="description" rows="3"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <div class="form-group">
                    <label>Categories *</label>
                    <div id="categories-container">
                        <?php
                        $oldCategories = $_POST['category'] ?? [''];
                        foreach ($oldCategories as $cat): ?>
                            <div class="category-step">
                                <select name="category[]" required>
                                    <option value="">Select category…</option>
                                    <?php foreach ($cats as $c): 
                                        $sel = ($cat === $c) ? 'selected' : '';
                                    ?>
                                        <option value="<?= htmlspecialchars($c) ?>" <?= $sel ?>><?= htmlspecialchars($c) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" class="remove-step">✖</button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" id="add-category" class="btn btn-secondary">+ Add Category</button>
                </div>
            </div>

            <div class="form-group">
                <label for="difficulty">Difficulty</label>
                <select id="difficulty" name="difficulty">
                    <?php foreach (['easy' => 'Easy','medium' => 'Medium','hard' => 'Hard'] as $val => $label): ?>
                        <?php $sel = (($_POST['difficulty'] ?? 'easy') === $val) ? 'selected' : ''; ?>
                        <option value="<?= $val ?>" <?= $sel ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Prep Time & Servings -->
        <div class="form-section">
            <label for="prep_time">Prep Time (minutes)</label>
            <input type="number" id="prep_time" name="prep_time" min="0" value="<?= htmlspecialchars($_POST['prep_time'] ?? '') ?>">

            <label for="servings">Servings</label>
            <input type="number" id="servings" name="servings" min="1" value="<?= htmlspecialchars($_POST['servings'] ?? '') ?>">
        </div>

        <!-- Ingredients step by step -->
        <div class="form-section">
            <label>Ingredients *</label>
            <div id="ingredients-container">
                <?php
                $oldIngredients = $_POST['ingredients'] ?? [''];
                foreach ($oldIngredients as $ing): ?>
                    <div class="ingredient-step">
                        <input type="text" name="ingredients[]" value="<?= htmlspecialchars($ing) ?>" required>
                        <button type="button" class="remove-step">✖</button>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" id="add-ingredient" class="btn btn-secondary">+ Add Ingredient</button>
        </div>

        <!-- Instructions step by step -->
        <div class="form-section">
            <label>Instructions *</label>
            <div id="instructions-container">
                <?php
                $oldInstructions = $_POST['instructions'] ?? [''];
                foreach ($oldInstructions as $inst): ?>
                    <div class="instruction-step">
                        <input type="text" name="instructions[]" value="<?= htmlspecialchars($inst) ?>" required>
                        <button type="button" class="remove-step">✖</button>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" id="add-instruction" class="btn btn-secondary">+ Add Step</button>
        </div>

        <div class="form-actions">
            <a href="<?= SITE_URL ?>/index.php?page=recipes" class="btn btn-ghost">Cancel</a>
            <button type="submit" class="btn btn-primary">Publish Recipe →</button>
        </div>
    </form>
</div>

<?php require_once 'footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    function addStep(containerId) {
        const container = document.getElementById(containerId);
        const div = document.createElement('div');
        div.classList.add(containerId === 'ingredients-container' ? 'ingredient-step' : 'instruction-step');
        div.innerHTML = '<input type="text" name="' + (containerId === 'ingredients-container' ? 'ingredients[]' : 'instructions[]') + '" required> <button type="button" class="remove-step">✖</button>';
        container.appendChild(div);
    }

    document.getElementById('add-ingredient').addEventListener('click', () => addStep('ingredients-container'));
    document.getElementById('add-instruction').addEventListener('click', () => addStep('instructions-container'));

    document.addEventListener('click', (e) => {
        if (e.target.classList.contains('remove-step')) {
            e.target.parentElement.remove();
        }
    });

    function addCategoryStep() {
        const container = document.getElementById('categories-container');
        const div = document.createElement('div');
        div.classList.add('category-step');

        // Créez le select avec les options
        let selectHTML = '<select name="category[]" required>';
        selectHTML += '<option value="">Select category…</option>';
        <?php foreach ($cats as $c): ?>
            selectHTML += '<option value="<?= htmlspecialchars($c) ?>"><?= htmlspecialchars($c) ?></option>';
        <?php endforeach; ?>
        selectHTML += '</select> <button type="button" class="remove-step">✖</button>';

        div.innerHTML = selectHTML;
        container.appendChild(div);
    }

    document.getElementById('add-category').addEventListener('click', addCategoryStep);
});
</script>