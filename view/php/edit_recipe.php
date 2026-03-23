<?php
$pageTitle = "Edit Recipe"; // Titre de la page
require_once 'header.php'; // Inclut header
?>
<div class="container" style="padding:20px;"> <!-- Conteneur -->

    <h2>Edit Recipe: <?= htmlspecialchars($recipe['title']) ?></h2> <!-- Titre recette -->
    <?php if(!empty($error)): ?>
        <p style="color:red;"><?= $error ?></p> <!-- Message erreur -->
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data"> <!-- Formulaire édition -->

        <label>Recipe Image</label>
        <?php if (!empty($recipe['image'])): ?>
            <div style="margin-bottom:10px;">
                <img src="<?= SITE_URL . '/' . $recipe['image'] ?>" style="width:150px;border-radius:10px;"> <!-- Aperçu image -->
            </div>
        <?php endif; ?>
        <input type="file" name="image" accept="image/*"> <!-- Upload image -->
        <label><input type="checkbox" name="delete_image"> Remove current image</label> <!-- Supprimer image -->

        <label>Title</label>
        <input type="text" name="title" value="<?= htmlspecialchars($recipe['title']) ?>" required> <!-- Titre -->

        <label>Description</label>
        <textarea name="description"><?= htmlspecialchars($recipe['description']) ?></textarea> <!-- Description -->

        <label>Categories *</label>
        <div id="categories-container"> <!-- Conteneur catégories -->
            <?php 
            $oldCategories = explode(',', $recipe['category']);
            if(empty($oldCategories)) $oldCategories = [''];
            foreach($oldCategories as $cat): ?>
                <div class="category-step">
                    <select name="category[]" required> <!-- Select catégorie -->
                        <option value="">Select category…</option>
                        <?php foreach($cats as $c): ?>
                            <option value="<?= htmlspecialchars($c) ?>" <?= $cat === $c ? 'selected' : '' ?>><?= htmlspecialchars($c) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" class="remove-step">✖</button> <!-- Supprimer catégorie -->
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" id="add-category" class="btn btn-secondary">+ Add Category</button> <!-- Ajouter catégorie -->

        <label>Difficulty</label>
        <select name="difficulty"> <!-- Difficulté -->
            <?php foreach(['easy','medium','hard'] as $diff): ?>
                <option value="<?= $diff ?>" <?= $recipe['difficulty']==$diff ? 'selected' : '' ?>><?= ucfirst($diff) ?></option>
            <?php endforeach; ?>
        </select>

        <!-- Prep Time & Servings -->
        <label>Prep Time (min)</label>
        <input type="number" name="prep_time" value="<?= $recipe['prep_time'] ?? 0 ?>"> <!-- Temps préparation -->

        <label>Servings</label>
        <input type="number" name="servings" value="<?= $recipe['servings'] ?? 0 ?>"> <!-- Portions -->

        <!-- Ingredients dynamic -->
        <label>Ingredients *</label>
        <div id="ingredients-container"> <!-- Conteneur ingrédients -->
            <?php foreach(explode("\n", $recipe['ingredients']) as $ing): ?>
                <div class="ingredient-step">
                    <input type="text" name="ingredients[]" value="<?= htmlspecialchars($ing) ?>" required> <!-- Ingrédient -->
                    <button type="button" class="remove-step">✖</button> <!-- Supprimer ingrédient -->
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" id="add-ingredient" class="btn btn-secondary">+ Add Ingredient</button> <!-- Ajouter ingrédient -->

        <!-- Instructions dynamic -->
        <label>Instructions *</label>
        <div id="instructions-container"> <!-- Conteneur instructions -->
            <?php foreach(explode("\n", $recipe['instructions']) as $inst): ?>
                <div class="instruction-step">
                    <input type="text" name="instructions[]" value="<?= htmlspecialchars($inst) ?>" required> <!-- Étape -->
                    <button type="button" class="remove-step">✖</button> <!-- Supprimer étape -->
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" id="add-instruction" class="btn btn-secondary">+ Add Step</button> <!-- Ajouter étape -->

        <button type="submit" class="btn btn-primary" style="margin-top:10px;">Save Changes</button> <!-- Sauvegarder -->
    </form>
</div>

<?php require_once 'footer.php'; ?> <!-- Pied de page -->

<script>
document.addEventListener('DOMContentLoaded', () => {

    function addStep(containerId) { // Ajouter étape ingrédient/instruction
        const container = document.getElementById(containerId);
        const div = document.createElement('div');
        div.classList.add(containerId === 'ingredients-container' ? 'ingredient-step' : 'instruction-step');
        div.innerHTML = '<input type="text" name="' + (containerId === 'ingredients-container' ? 'ingredients[]' : 'instructions[]') + '" required> <button type="button" class="remove-step">✖</button>';
        container.appendChild(div);
    }

    document.getElementById('add-ingredient').addEventListener('click', () => addStep('ingredients-container')); // Bouton ajouter ingrédient
    document.getElementById('add-instruction').addEventListener('click', () => addStep('instructions-container')); // Bouton ajouter étape

    document.addEventListener('click', (e) => { // Supprimer étape
        if (e.target.classList.contains('remove-step')) {
            e.target.parentElement.remove();
        }
    });

    function addCategoryStep() { // Ajouter catégorie
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

    document.getElementById('add-category').addEventListener('click', addCategoryStep); // Bouton ajouter catégorie
});
</script>