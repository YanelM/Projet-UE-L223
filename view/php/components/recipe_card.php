<?php
// sécurité
if (!isset($recipe)) return; // Si pas de recette, stop

// nombre de likes
$likes = $recipe['likes'] ?? 0; // likes ou 0
?>

<a href="<?= SITE_URL ?>/index.php?page=recipe&id=<?= $recipe['id'] ?>" 
   class="recipe-card-link" 
   style="text-decoration:none;color:inherit; display:block;"> <!-- Lien vers recette -->

    <article class="recipe-card" style="display:flex;align-items:center;gap:15px; padding:10px; border:1px solid #eee; border-radius:12px; transition:box-shadow 0.2s;">
        
        <!-- IMAGE -->
        <div style="width:80px;height:80px;flex-shrink:0;">
            <?php if (!empty($recipe['image'])): ?>
                <img src="<?= SITE_URL . '/' . $recipe['image'] ?>" 
                     style="width:100%;height:100%;object-fit:cover;border-radius:12px;"> <!-- Image -->
            <?php else: ?>
                <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#eee;border-radius:12px;">
                    🍽️ <!-- Placeholder si pas d'image -->
                </div>
            <?php endif; ?>
        </div>

        <!-- CONTENT -->
        <div style="flex:1;">
            <div style="font-weight:600;">
                <?= htmlspecialchars($recipe['title']) ?> <!-- Titre -->
            </div>

            <div style="font-size:12px;color:gray;">
                by <?= htmlspecialchars($recipe['username']) ?> <!-- Auteur -->
            </div>

            <div style="font-size:13px;margin-top:5px;">
                ❤️ <?= $likes ?> <!-- Likes -->
            </div>
        </div>

    </article>
</a>

<!-- Petit hover effect -->
<style>
.recipe-card-link:hover article {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1); /* effet hover */
}
</style>