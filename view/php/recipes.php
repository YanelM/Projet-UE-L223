<?php
$pageTitle = "Recipes — Cook n' Share"; // Titre de la page
require_once 'header.php'; // Inclut l'en-tête
?>

<div class="recipes-page">

    <!-- HEADER -->
    <div class="recipes-page-header">
        <div>
            <h1>All Recipes</h1> <!-- Titre principal -->
        </div>

        <form method="GET" action="<?= SITE_URL ?>/index.php" style="display:flex;gap:.5rem;align-items:center;">
            <input type="hidden" name="page" value="recipes"> <!-- Page actuelle -->

            <?php if ($selectedCat): ?>
                <input type="hidden" name="category" value="<?= htmlspecialchars($selectedCat) ?>"> <!-- Catégorie sélectionnée -->
            <?php endif; ?>

            <input type="search" name="search" class="form-control"
                   placeholder="Search recipes…"
                   value="<?= htmlspecialchars($search) ?>" 
                   style="max-width:260px;margin:0">

            <button type="submit" class="btn btn-primary"
                    style="padding:.72rem 1.25rem;font-size:.9rem;">
                Search <!-- Bouton rechercher -->
            </button>

            <?php if ($search || $selectedCat): ?>
                <a href="<?= SITE_URL ?>/index.php?page=recipes"
                   class="btn"
                   style="padding:.72rem 1rem;font-size:.9rem;background:var(--border);">
                    Clear <!-- Bouton pour réinitialiser -->
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- FILTERS -->
    <div class="filter-bar-wrapper">
        <div class="filter-bar">
            <a href="<?= SITE_URL ?>/index.php?page=recipes<?= $search ? '&search=' . urlencode($search) : '' ?>"
            class="filter-btn <?= !$selectedCat ? 'active' : '' ?>">All</a> <!-- Filtre tout -->

            <?php foreach ($categories as $cat): ?>
                <a href="<?= SITE_URL ?>/index.php?page=recipes&category=<?= urlencode($cat) ?><?= $search ? '&search=' . urlencode($search) : '' ?>"
                class="filter-btn <?= $selectedCat === $cat ? 'active' : '' ?>">
                    <?= htmlspecialchars($cat) ?> <!-- Filtre par catégorie -->
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- CONTENT -->
    <?php if (empty($recipes)): ?>
        <div class="empty-state">
            <h3>No recipes found</h3> <!-- Message si aucune recette -->
            <p>
                Try a different search or category, or
                <a href="<?= SITE_URL ?>/index.php?page=add_recipe">
                    add the first one
                </a>! <!-- Lien ajouter recette -->
            </p>
        </div>
    <?php else: ?>

        <!-- 🔥 LIST (plus grid) -->
        <div class="recipes-list">

            <?php foreach ($recipes as $recipe): ?>
                <?php include 'components/recipe_card.php'; ?> <!-- Carte recette -->
            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>

<?php require_once 'footer.php'; ?> <!-- Inclut le pied de page -->