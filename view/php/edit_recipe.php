<?php
$pageTitle = "Edit Recipe";
require_once 'header.php';
?>
<div class="container" style="padding:20px;">

    <h2>Edit Recipe: <?= htmlspecialchars($recipe['title']) ?></h2>
    <?php if(!empty($error)): ?>
        <p style="color:red;"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">

        <label>Recipe Image</label>
        <?php if (!empty($recipe['image'])): ?>
            <div style="margin-bottom:10px;">
                <img src="<?= SITE_URL . '/' . $recipe['image'] ?>" style="width:150px;border-radius:10px;">
            </div>
        <?php endif; ?>
        <input type="file" name="image" accept="image/*">
        <label><input type="checkbox" name="delete_image"> Remove current image</label>

        <label>Title</label>
        <input type="text" name="title" value="<?= htmlspecialchars($recipe['title']) ?>" required>

        <label>Description</label>
        <textarea name="description"><?= htmlspecialchars($recipe['description']) ?></textarea>

        <label>Categories *</label>
        <div id="categories-container">
            <?php 
            $oldCategories = explode(',', $recipe['category']);
            if(empty($oldCategories)) $oldCategories = [''];
            foreach($oldCategories as $cat): ?>
                <div class="category-step">
                    <select name="category[]" required>
                        <option value="">Select category…</option>
                        <?php foreach($cats as $c): ?>
                            <option value="<?= htmlspecialchars($c) ?>" <?= $cat === $c ? 'selected' : '' ?>><?= htmlspecialchars($c) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" class="remove-step">✖</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" id="add-category" class="btn btn-secondary">+ Add Category</button>

        <label>Difficulty</label>
        <select name="difficulty">
            <?php foreach(['easy','medium','hard'] as $diff): ?>
                <option value="<?= $diff ?>" <?= $recipe['difficulty']==$diff ? 'selected' : '' ?>><?= ucfirst($diff) ?></option>
            <?php endforeach; ?>
        </select>

        <!-- Prep Time & Servings -->
        <label>Prep Time (min)</label>
        <input type="number" name="prep_time" value="<?= $recipe['prep_time'] ?? 0 ?>">

        <label>Servings</label>
        <input type="number" name="servings" value="<?= $recipe['servings'] ?? 0 ?>">

        <!-- Ingredients dynamic -->
        <label>Ingredients *</label>
        <div id="ingredients-container">
            <?php foreach(explode("\n", $recipe['ingredients']) as $ing): ?>
                <div class="ingredient-step">
                    <input type="text" name="ingredients[]" value="<?= htmlspecialchars($ing) ?>" required>
                    <button type="button" class="remove-step">✖</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" id="add-ingredient" class="btn btn-secondary">+ Add Ingredient</button>

        <!-- Instructions dynamic -->
        <label>Instructions *</label>
        <div id="instructions-container">
            <?php foreach(explode("\n", $recipe['instructions']) as $inst): ?>
                <div class="instruction-step">
                    <input type="text" name="instructions[]" value="<?= htmlspecialchars($inst) ?>" required>
                    <button type="button" class="remove-step">✖</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" id="add-instruction" class="btn btn-secondary">+ Add Step</button>

        <button type="submit" class="btn btn-primary" style="margin-top:10px;">Save Changes</button>
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

        // Construire le select
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