<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// ============================================================
//  Cook 'n' Share — Browse Recipes
// ============================================================

require_once '../includes/config.php';
$pageTitle = "Browse Recipes — Cook 'n' Share";

$db = getDB();

// Get categories for filter bar
$categories = $db->query("SELECT DISTINCT category FROM recipes ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);

// Filter by category
$selectedCat = $_GET['category'] ?? '';
$search      = trim($_GET['search'] ?? '');

$sql    = "SELECT r.*, u.username FROM recipes r JOIN users u ON r.user_id = u.id WHERE 1=1";
$params = [];

if ($selectedCat) {
    $sql    .= " AND r.category = ?";
    $params[] = $selectedCat;
}

if ($search) {
    $sql    .= " AND (r.title LIKE ? OR r.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " ORDER BY r.created_at DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$recipes = $stmt->fetchAll();

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

require_once '../includes/header.php';
?>

<div class="recipes-page">

    <div class="recipes-page-header">
        <div>
            <h1>All Recipes</h1>
            <p class="text-muted"><?= count($recipes) ?> recipe<?= count($recipes) !== 1 ? 's' : '' ?> found</p>
        </div>

        <form method="GET" style="display:flex;gap:.5rem;align-items:center;">
            <?php if ($selectedCat): ?>
                <input type="hidden" name="category" value="<?= htmlspecialchars($selectedCat) ?>">
            <?php endif; ?>

            <input
                type="search"
                name="search"
                class="form-control"
                placeholder="Search recipes…"
                value="<?= htmlspecialchars($search) ?>"
                style="max-width:260px;margin:0"
            >

            <button type="submit" class="btn btn-primary" style="padding:.72rem 1.25rem;font-size:.9rem;">
                Search
            </button>

            <?php if ($search || $selectedCat): ?>
                <a href="recipes.php" class="btn" style="padding:.72rem 1rem;font-size:.9rem;background:var(--border);color:var(--text-body)">
                    Clear
                </a>
            <?php endif; ?>
        </form>
    </div>

    <?php if (!empty($categories)): ?>
    <div class="filter-bar">

        <a href="recipes.php<?= $search ? '?search=' . urlencode($search) : '' ?>"
           class="filter-btn <?= !$selectedCat ? 'active' : '' ?>">
            All
        </a>

        <?php foreach ($categories as $cat): ?>
            <a href="recipes.php?category=<?= urlencode($cat) ?><?= $search ? '&search=' . urlencode($search) : '' ?>"
               class="filter-btn <?= $selectedCat === $cat ? 'active' : '' ?>">
                <?= ($categoryIcons[$cat] ?? '🍽️') . ' ' . htmlspecialchars($cat) ?>
            </a>
        <?php endforeach; ?>

    </div>
    <?php endif; ?>

    <?php if (empty($recipes)): ?>

        <div class="empty-state">
            <div class="empty-icon">🔍</div>
            <h3>No recipes found</h3>
            <p>
                Try a different search or category, or
                <a href="../recipes/add_recipe.php" style="color:var(--amber)">add the first one</a>!
            </p>
        </div>

    <?php else: ?>

        <div class="recipes-grid">

            <?php foreach ($recipes as $i => $recipe): ?>

                <?php
                    $icon      = $categoryIcons[$recipe['category']] ?? '🍽️';
                    $totalTime = ($recipe['prep_time'] + $recipe['cook_time']);
                ?>

                <article class="recipe-card" style="animation-delay:<?= min($i * .08, .6) ?>s">

                    <div class="card-image">
                        <?= $icon ?>
                        <span class="card-badge"><?= htmlspecialchars($recipe['category']) ?></span>
                    </div>

                    <div class="card-body">

                        <div class="card-category"><?= htmlspecialchars($recipe['category']) ?></div>

                        <h3 class="card-title"><?= htmlspecialchars($recipe['title']) ?></h3>

                        <p class="card-desc"><?= htmlspecialchars($recipe['description']) ?></p>

                        <div class="card-meta">

                            <?php if ($totalTime > 0): ?>
                                <span class="meta-item">
                                    <span class="meta-icon">⏱</span> <?= $totalTime ?> min
                                </span>
                            <?php endif; ?>

                            <?php if ($recipe['servings']): ?>
                                <span class="meta-item">
                                    <span class="meta-icon">🍽️</span> <?= $recipe['servings'] ?> servings
                                </span>
                            <?php endif; ?>

                            <span class="meta-item difficulty-<?= $recipe['difficulty'] ?>">
                                <?= ucfirst($recipe['difficulty']) ?>
                            </span>

                        </div>

                        <div style="display:flex;justify-content:space-between;align-items:center;margin-top:1rem">

                            <span style="font-size:.8rem;color:var(--text-muted)">
                                by <?= htmlspecialchars($recipe['username']) ?>
                            </span>

                            <a href="../recipes/recipe.php?id=<?= $recipe['id'] ?>" class="card-link" style="margin:0">
                                View →
                            </a>

                        </div>

                    </div>

                </article>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>

<?php require_once '../includes/footer.php'; ?>