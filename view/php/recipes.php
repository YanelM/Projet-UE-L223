<?php
$pageTitle = "Recipes — Cook n' Share";
require_once 'header.php';
?>

<div class="recipes-page">

    <!-- HEADER -->
    <div class="recipes-page-header">
        <div>
            <h1>All Recipes</h1>
        </div>

        <form method="GET" action="<?= SITE_URL ?>/index.php" style="display:flex;gap:.5rem;align-items:center;">
            <input type="hidden" name="page" value="recipes">

            <?php if ($selectedCat): ?>
                <input type="hidden" name="category" value="<?= htmlspecialchars($selectedCat) ?>">
            <?php endif; ?>

            <input type="search" name="search" class="form-control"
                   placeholder="Search recipes…"
                   value="<?= htmlspecialchars($search) ?>"
                   style="max-width:260px;margin:0">

            <button type="submit" class="btn btn-primary"
                    style="padding:.72rem 1.25rem;font-size:.9rem;">
                Search
            </button>

            <?php if ($search || $selectedCat): ?>
                <a href="<?= SITE_URL ?>/index.php?page=recipes"
                   class="btn"
                   style="padding:.72rem 1rem;font-size:.9rem;background:var(--border);">
                    Clear
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- FILTERS -->
    <div class="filter-bar-wrapper">
        <div class="filter-bar">
            <a href="<?= SITE_URL ?>/index.php?page=recipes<?= $search ? '&search=' . urlencode($search) : '' ?>"
            class="filter-btn <?= !$selectedCat ? 'active' : '' ?>">All</a>

            <?php foreach ($categories as $cat): ?>
                <a href="<?= SITE_URL ?>/index.php?page=recipes&category=<?= urlencode($cat) ?><?= $search ? '&search=' . urlencode($search) : '' ?>"
                class="filter-btn <?= $selectedCat === $cat ? 'active' : '' ?>">
                    <?= htmlspecialchars($cat) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- CONTENT -->
    <?php if (empty($recipes)): ?>
        <div class="empty-state">
            <h3>No recipes found</h3>
            <p>
                Try a different search or category, or
                <a href="<?= SITE_URL ?>/index.php?page=add_recipe">
                    add the first one
                </a>!
            </p>
        </div>
    <?php else: ?>

        <!-- 🔥 LIST (plus grid) -->
        <div class="recipes-list">

            <?php foreach ($recipes as $recipe): ?>
                <?php include 'components/recipe_card.php'; ?>
            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>

<?php require_once 'footer.php'; ?>