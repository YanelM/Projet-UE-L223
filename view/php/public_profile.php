<?php
$pageTitle = htmlspecialchars($user['username']) . " — Cook n' Share";
require_once 'header.php';
?>

<div class="container">

    <!-- USER INFO -->
    <div style="display:flex;align-items:center;gap:15px;margin-bottom:20px;">
        <div style="width:80px;height:80px;flex-shrink:0;">
            <?php if (!empty($user['avatar'])): ?>
                <img src="<?= SITE_URL . '/' . $user['avatar'] ?>" style="width:100%;height:100%;border-radius:50%;object-fit:cover;">
            <?php else: ?>
                <div style="width:100%;height:100%;border-radius:50%;background:#ccc;display:flex;align-items:center;justify-content:center;font-size:30px;">
                    <?= strtoupper($user['username'][0]) ?>
                </div>
            <?php endif; ?>
        </div>
        <div style="flex:1;">
            <div style="font-weight:600;font-size:18px;"><?= htmlspecialchars($user['username']) ?></div>
            
            <!-- ❤️ cœur cliquable -->
            <?php if(isset($_SESSION['user']) && $_SESSION['user']['id'] !== $user['id']): ?>
                <a href="<?= SITE_URL ?>/index.php?page=toggleLike&id=<?= $user['id'] ?>" 
                    style="font-size:20px;color:<?= $isLiked ? 'red' : '#555' ?>; text-decoration:none;">
                    ❤️ <?= $likesCount ?>
                </a>
            <?php else: ?>
                <span style="font-size:20px;color:#555;">❤️ <?= $likesCount ?></span>
            <?php endif; ?>
        </div>
    </div>

    <!-- USER RECIPES -->
    <h3><?= htmlspecialchars($user['username']) ?>'s Recipes</h3>

    <div class="recipes-list">
        <?php if (!empty($recipes)): ?>
            <?php foreach ($recipes as $r): 
                $categoryIcons = [
                    'Pasta'      => '🍝', 'Vegetarian' => '🥗', 'Dessert' => '🍰',
                    'Soup'       => '🍲', 'Seafood' => '🦞', 'Meat' => '🥩',
                    'Breakfast'  => '🥞', 'Salad' => '🥙',
                ];
                $icon = $categoryIcons[$r['category']] ?? '🍽️';
                $totalFavs = $r['likes'] ?? 0;
            ?>
                <a href="<?= SITE_URL ?>/index.php?page=recipe&id=<?= $r['id'] ?>" class="recipe-card-link" style="text-decoration:none;color:inherit; display:block;">
                    <article class="recipe-card" style="display:flex;align-items:center;gap:15px;padding:10px;border:1px solid #eee;border-radius:12px;margin-bottom:12px;transition:box-shadow 0.2s;">
                        <!-- IMAGE -->
                        <div style="width:80px;height:80px;flex-shrink:0;">
                            <?php if (!empty($r['image'])): ?>
                                <img src="<?= SITE_URL . '/' . $r['image'] ?>" style="width:100%;height:100%;object-fit:cover;border-radius:12px;">
                            <?php else: ?>
                                <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#eee;border-radius:12px;font-size:30px;">
                                    <?= $icon ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- CONTENT -->
                        <div style="flex:1;">
                            <div style="font-weight:600;font-size:15px;">
                                <?= htmlspecialchars($r['title']) ?>
                            </div>
                            <div style="font-size:12px;color:gray;margin-top:4px;">
                                by <?= htmlspecialchars($r['username']) ?>
                            </div>
                        </div>

                        <!-- LIKES -->
                        <div style="font-size:13px;margin-top:6px;color:#555;">
                            ❤️ <?= $totalFavs ?>
                        </div>
                    </article>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align:center;color:gray;">No recipes found</p>
        <?php endif; ?>
    </div>

    <!-- BACK BUTTON -->
    <div style="margin-top:30px;">
        <a href="<?= SITE_URL ?>/index.php?page=recipes" class="btn">← Back</a>
    </div>

</div>

<style>
.recipe-card-link:hover article {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
</style>

<?php require_once 'footer.php'; ?>