<?php
$pageTitle = "Recipe — Cook n' Share";
require_once 'header.php';

$totalFavs = Recipe::countFavorites($recipe['id'] ?? 0);

$userFavored = false;
if(isset($_SESSION['user'])) {
    $stmt = Database::getInstance()->prepare("
        SELECT 1 FROM recipe_favorites WHERE user_id=? AND recipe_id=?
    ");
    $stmt->execute([$_SESSION['user']['id'], $recipe['id'] ?? 0]);
    $userFavored = (bool)$stmt->fetchColumn();
}

$isOwner = isset($_SESSION['user']) && $_SESSION['user']['id'] === $recipe['user_id'];
?>

<div class="container">

    <!-- IMAGE -->
    <?php if (!empty($recipe['image'])): ?>
        <div style="margin-bottom:15px;">
            <img src="<?= SITE_URL . '/' . $recipe['image'] ?>" style="width:100%;border-radius:12px;">
        </div>
    <?php endif; ?>

    <!-- USER & LIKES -->
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;">
        <div style="display:flex; align-items:center; gap:8px;">
            <?php if (!empty($recipe['avatar'])): ?>
                <a href="<?= SITE_URL ?>/index.php?page=public_profile&id=<?= $recipe['user_id'] ?>">
                    <img src="<?= SITE_URL . $recipe['avatar'] ?>" style="width:32px; height:32px; border-radius:50%;">
                </a>
            <?php endif; ?>
            <a href="<?= SITE_URL ?>/index.php?page=public_profile&id=<?= $recipe['user_id'] ?>" style="text-decoration:none; color:#333; font-weight:600;">
                <?= htmlspecialchars($recipe['username']) ?>
            </a>
        </div>

        <div style="display:flex;align-items:center;gap:10px;">
            <?php if(isset($_SESSION['user'])): ?>
                <a href="<?= SITE_URL ?>/index.php?page=toggle_favorite&id=<?= $recipe['id'] ?>"
                style="font-size:14px;color:<?= $userFavored ? 'red' : '#555' ?>; text-decoration:none;">
                    ❤️ <?= $totalFavs ?>
                </a>
            <?php else: ?>
                <span style="font-size:14px;color:#555;">❤️ <?= $totalFavs ?></span>
            <?php endif; ?>
        </div>
    </div>

    <!-- TITLE & META -->
    <div style="font-weight:600;font-size:18px;margin-bottom:5px;"><?= htmlspecialchars($recipe['title']) ?></div>
    <div style="font-size:13px;color:gray;margin-bottom:10px;">
        ⏱ <?= $totalTime ?> min · <?= ucfirst($recipe['difficulty']) ?>
        <?php if ($recipe['servings']): ?> · 🍽 <?= $recipe['servings'] ?><?php endif; ?>
    </div>

    <!-- DESCRIPTION -->
    <?php if ($recipe['description']): ?>
        <div style="margin-bottom:15px;"><?= htmlspecialchars($recipe['description']) ?></div>
    <?php endif; ?>

    <!-- BUTTONS: INGREDIENTS / INSTRUCTIONS -->
    <div style="display:flex;gap:10px;margin-bottom:15px;">
        <button type="button" onclick="showTab('ingredients')" class="tab-btn active">Ingredients</button>
        <button type="button" onclick="showTab('instructions')" class="tab-btn">Instructions</button>
    </div>

    <!-- INGREDIENTS -->
    <div id="ingredients-tab">
        <?php if ($ingredients): ?>
            <ul style="margin-bottom:15px;">
                <?php foreach ($ingredients as $item): ?>
                    <li><?= htmlspecialchars($item) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p style="color:gray;">No ingredients</p>
        <?php endif; ?>
    </div>

    <!-- INSTRUCTIONS -->
    <div id="instructions-tab" style="display:none;">
        <?php if ($instructions): ?>
            <ol style="margin-bottom:15px;">
                <?php foreach ($instructions as $step): ?>
                    <li><?= htmlspecialchars($step) ?></li>
                <?php endforeach; ?>
            </ol>
        <?php else: ?>
            <p style="color:gray;">No instructions</p>
        <?php endif; ?>
    </div>

    <!-- COMMENTS -->
    <div style="margin-top:20px;">
        <h3>💬 Comments</h3>

        <?php if(isset($_SESSION['user'])): ?>
            <form method="POST" action="index.php?page=add_comment" style="margin-bottom:15px;">
                <input type="hidden" name="recipe_id" value="<?= $recipe['id'] ?>">
                <textarea name="content" placeholder="Write a comment..." required style="width:100%;margin-bottom:5px;"></textarea>
                <button type="submit" class="btn btn-primary">Post Comment</button>
            </form>
        <?php else: ?>
            <p style="color:gray;">You must <a href="index.php?page=login">login</a> to comment.</p>
        <?php endif; ?>

        <hr style="margin:15px 0;">

        <?php
        function displayComments($comments, $level = 0) {
            foreach ($comments as $c) {
                echo '<div style="margin-left:' . ($level*20) . 'px;margin-bottom:12px;">';
                echo '<strong>' . htmlspecialchars($c['username']) . '</strong>';
                echo '<div style="font-size:12px;color:gray;">' . $c['created_at'] . '</div>';
                echo '<p>' . htmlspecialchars($c['content']) . '</p>';

                if (isset($_SESSION['user'])) {
                    echo '<button type="button" onclick="toggleReplyForm(' . $c['id'] . ')" style="font-size:12px;color:gray;background:none;border:none;cursor:pointer;">↩️ Reply</button>';
                    echo '<form method="POST" action="index.php?page=add_comment" id="reply-form-' . $c['id'] . '" style="margin-top:5px; display:none;">';
                    echo '<input type="hidden" name="recipe_id" value="' . $c['recipe_id'] . '">';
                    echo '<input type="hidden" name="parent_id" value="' . $c['id'] . '">';
                    echo '<input type="text" name="content" placeholder="Reply..." required>';
                    echo '<button type="submit">Send</button>';
                    echo '</form>';
                }

                if (!empty($c['replies'])) {
                    displayComments($c['replies'], $level+1);
                }

                echo '</div>';
            }
        }

        if(!empty($comments)) {
            displayComments($comments);
        } else {
            echo '<p style="color:gray;">No comments yet.</p>';
        }
        ?>
    </div>

    <!-- BACK BUTTON -->
    <div style="margin-top:20px;">
        <a href="<?= SITE_URL ?>/index.php?page=recipes" class="btn">← Back</a>

        <?php if ($isOwner): ?>
            <!-- Bouton Modifier -->
            <a href="<?= SITE_URL ?>/index.php?page=edit_recipe&id=<?= $recipe['id'] ?>" 
            class="btn btn-sm" 
            style="font-size:12px;padding:.2rem .5rem;">
            Edit
            </a>
            <?php if(isset($_SESSION['error'])): ?>
                <div style="color:red; margin-bottom:10px;">
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php if(isset($_SESSION['success'])): ?>
                <div style="color:green; margin-bottom:10px;">
                    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            <!-- Bouton Supprimer -->
            <a href="<?= SITE_URL ?>/index.php?page=delete_recipe&id=<?= $recipe['id'] ?>" 
            class="btn btn-sm btn-danger" 
            style="font-size:12px;padding:.2rem .5rem;" 
            onclick="return confirm('Are you sure you want to delete this recipe?');">
            Delete
            </a>
        <?php endif; ?>
    </div>

</div>

<!-- SCRIPTS -->
<script>
function showTab(tab) {
    document.getElementById('ingredients-tab').style.display = (tab==='ingredients') ? 'block' : 'none';
    document.getElementById('instructions-tab').style.display = (tab==='instructions') ? 'block' : 'none';

    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelector('.tab-btn[onclick="showTab(\''+tab+'\')"]').classList.add('active');
}

function toggleReplyForm(id) {
    document.querySelectorAll("[id^='reply-form-']").forEach(f => {
        if (f.id !== "reply-form-" + id) f.style.display = "none";
    });
    const form = document.getElementById("reply-form-" + id);
    form.style.display = (form.style.display === "none") ? "block" : "none";
}
</script>

<?php require_once 'footer.php'; ?>