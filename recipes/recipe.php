<?php
require_once '../includes/config.php';
require_once '../includes/header.php';

$id = (int)($_GET['id'] ?? 0);
$db = getDB();

$stmt = $db->prepare("SELECT r.*, u.username FROM recipes r JOIN users u ON r.user_id = u.id WHERE r.id = ?");
$stmt->execute([$id]);
$recipe = $stmt->fetch();

if (!$recipe) {
    echo '<div class="empty-state"><span class="empty-icon">🔍</span><h3>Recipe not found</h3><p><a href="recipes.php">Browse all recipes</a></p></div>';
    require_once '../includes/footer.php';
    exit;
}

$categoryIcons = [
    'Pasta' => '🍝', 'Vegetarian' => '🥗', 'Dessert' => '🍰',
    'Soup'  => '🍲', 'Seafood'    => '🦞', 'Meat'    => '🥩',
    'Breakfast' => '🥞', 'Salad'  => '🥙',
];

$icon      = $categoryIcons[$recipe['category']] ?? '🍽️';
$totalTime = ($recipe['prep_time'] ?? 0) + ($recipe['cook_time'] ?? 0);

$ingredients  = array_filter(array_map('trim', explode("\n", $recipe['ingredients'])));
$instructions = array_filter(array_map('trim', explode("\n", $recipe['instructions'])));
?>

<div class="recipe-page">

    <!-- Main content -->
    <div class="recipe-main">

        <div class="recipe-header">
            <div class="category-tag"><?= $icon ?> <?= htmlspecialchars($recipe['category']) ?></div>
            <h1><?= htmlspecialchars($recipe['title']) ?></h1>

            <?php if ($recipe['description']): ?>
                <p class="recipe-desc"><?= htmlspecialchars($recipe['description']) ?></p>
            <?php endif; ?>

            <div class="recipe-meta-row">
                <?php if ($totalTime > 0): ?>
                    <span class="meta-item"><span>⏱</span> <?= $totalTime ?> min total</span>
                <?php endif; ?>
                <?php if ($recipe['prep_time']): ?>
                    <span class="meta-item"><span>🔪</span> <?= $recipe['prep_time'] ?> min prep</span>
                <?php endif; ?>
                <?php if ($recipe['cook_time']): ?>
                    <span class="meta-item"><span>🔥</span> <?= $recipe['cook_time'] ?> min cook</span>
                <?php endif; ?>
                <?php if ($recipe['servings']): ?>
                    <span class="meta-item"><span>🍽️</span> <?= $recipe['servings'] ?> servings</span>
                <?php endif; ?>
                <span class="meta-item difficulty-<?= $recipe['difficulty'] ?>"><?= ucfirst($recipe['difficulty']) ?></span>
            </div>
        </div>

        <!-- Ingredients -->
        <div class="recipe-section">
            <h3>Ingredients</h3>
            <?php if ($ingredients): ?>
                <ul class="ingredient-list">
                    <?php foreach ($ingredients as $item): ?>
                        <li><?= htmlspecialchars($item) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p style="color:var(--text-muted)">No ingredients listed.</p>
            <?php endif; ?>
        </div>

        <!-- Instructions -->
        <div class="recipe-section">
            <h3>Instructions</h3>
            <?php if ($instructions): ?>
                <ol class="instruction-list">
                    <?php foreach ($instructions as $step): ?>
                        <li><?= htmlspecialchars($step) ?></li>
                    <?php endforeach; ?>
                </ol>
            <?php else: ?>
                <p style="color:var(--text-muted)">No instructions listed.</p>
            <?php endif; ?>
        </div>

        <div style="margin-top:36px; padding-top:24px; border-top:1px solid var(--border);">
            <a href="recipes.php" class="btn btn-ghost">← Back to recipes</a>
        </div>

    </div>

    <!-- Sidebar -->
    <aside class="recipe-sidebar">

        <div class="sidebar-section">
            <h4>At a glance</h4>
            <ul class="stat-list">
                <li>
                    <span class="stat-label">Category</span>
                    <span class="stat-value"><?= htmlspecialchars($recipe['category'] ?: '—') ?></span>
                </li>
                <li>
                    <span class="stat-label">Difficulty</span>
                    <span class="stat-value difficulty-<?= $recipe['difficulty'] ?>"
                        style="padding:3px 10px;border-radius:99px;font-size:.82rem;">
                        <?= ucfirst($recipe['difficulty']) ?>
                    </span>
                </li>
                <?php if ($recipe['servings']): ?>
                <li>
                    <span class="stat-label">Servings</span>
                    <span class="stat-value"><?= $recipe['servings'] ?></span>
                </li>
                <?php endif; ?>
                <?php if ($recipe['prep_time']): ?>
                <li>
                    <span class="stat-label">Prep time</span>
                    <span class="stat-value"><?= $recipe['prep_time'] ?> min</span>
                </li>
                <?php endif; ?>
                <?php if ($recipe['cook_time']): ?>
                <li>
                    <span class="stat-label">Cook time</span>
                    <span class="stat-value"><?= $recipe['cook_time'] ?> min</span>
                </li>
                <?php endif; ?>
                <?php if ($totalTime > 0): ?>
                <li>
                    <span class="stat-label">Total time</span>
                    <span class="stat-value"><?= $totalTime ?> min</span>
                </li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="sidebar-section">
            <h4>Shared by</h4>
            <p style="font-weight:600;color:var(--text-head)">👤 <?= htmlspecialchars($recipe['username']) ?></p>
            <?php if ($recipe['created_at']): ?>
                <p style="font-size:.8rem;color:var(--text-muted);margin-top:6px;">
                    <?= date('F j, Y', strtotime($recipe['created_at'])) ?>
                </p>
            <?php endif; ?>
        </div>

        <div class="sidebar-section">
            <a href="recipes.php" style="color:var(--text-muted);font-size:.87rem;">← Browse more recipes</a>
        </div>

    </aside>

</div>

<?php require_once '../includes/footer.php'; ?>
