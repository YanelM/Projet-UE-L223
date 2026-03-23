<?php
$pageTitle = $title; // Titre de la page
require_once 'header.php'; // Inclut l'en-tête
?>

<div class="container">

    <h2><?= $title ?></h2> <!-- Titre section -->

    <div class="recipes-list">
        <?php if (!empty($recipes)): ?>
            <?php foreach ($recipes as $r): 
                // Choisir une icône si pas d'image
                $categoryIcons = [
                    'Pasta'      => '🍝', 'Vegetarian' => '🥗', 'Dessert' => '🍰',
                    'Soup'       => '🍲', 'Seafood' => '🦞', 'Meat' => '🥩',
                    'Breakfast'  => '🥞', 'Salad' => '🥙',
                ];
                $icon = $categoryIcons[$r['category']] ?? '🍽️'; // Icône par défaut

                // Nombre de likes
                $likes = $r['likes'] ?? Recipe::countFavorites($r['id']); // Likes recette
            ?>
                <a href="<?= SITE_URL ?>/index.php?page=recipe&id=<?= $r['id'] ?>" 
                   class="recipe-card-link" 
                   style="text-decoration:none;color:inherit; display:block;">

                    <article class="recipe-card" style="display:flex;align-items:center;gap:15px;padding:10px;border:1px solid #eee;border-radius:12px;margin-bottom:12px;transition:box-shadow 0.2s;">
                        
                        <!-- IMAGE -->
                        <div style="width:80px;height:80px;flex-shrink:0;">
                            <?php if (!empty($r['image'])): ?>
                                <img src="<?= SITE_URL . '/' . $r['image'] ?>" 
                                     style="width:100%;height:100%;object-fit:cover;border-radius:12px;"> <!-- Image recette -->
                            <?php else: ?>
                                <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#eee;border-radius:12px;font-size:30px;">
                                    <?= $icon ?> <!-- Icône catégorie -->
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- CONTENT -->
                        <div style="flex:1;">
                            <div style="font-weight:600;font-size:15px;">
                                <?= htmlspecialchars($r['title']) ?> <!-- Titre recette -->
                            </div>

                            <div style="font-size:12px;color:gray;margin-top:4px;">
                                by <?= htmlspecialchars($r['username']) ?> <!-- Auteur -->
                            </div>
                        </div>

                        <!-- LIKES -->
                        <div style="font-size:13px;margin-top:6px;color:#555;">
                            ❤️ <?= $likes ?> <!-- Nombre de likes -->
                        </div>

                    </article>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align:center;color:gray;">No recipes found</p> <!-- Message si aucune recette -->
        <?php endif; ?>
    </div>

    <!-- BACK BUTTON -->
    <div style="margin-top:30px;">
        <a href="<?= SITE_URL ?>/index.php?page=profile" class="btn">
            ← Back to profile <!-- Bouton retour -->
        </a>
    </div>

</div>

<!-- Hover effect -->
<style>
.recipe-card-link:hover article {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1); /* Effet hover carte recette */
}
</style>

<?php require_once 'footer.php'; ?> <!-- Pied de page -->