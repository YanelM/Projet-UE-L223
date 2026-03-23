<?php
$pageTitle = "Cook n' Share"; // Titre de la page

$popularRecipes = Recipe::popular(3); // 3 recettes populaires
$latestRecipes  = Recipe::latest(10); // 10 dernières recettes

include 'header.php'; // Inclut l'en-tête
?>

<!-- HERO -->
<section class="hero" style="text-align:center;padding:40px 20px;">
    <h1>🍳 Cook n' Share</h1> <!-- Titre principal -->

    <h2>Discover & Share<br><em>Delicious Recipes</em></h2> <!-- Sous-titre -->

    <p style="max-width:500px;margin:15px auto;">
        Join our community of passionate home cooks. Find inspiration,
        share your creations, and explore dishes from around the world. <!-- Texte d'intro -->
    </p>

    <div style="margin-top:20px;display:flex;gap:10px;justify-content:center;">
        <a href="<?= SITE_URL ?>/index.php?page=recipes" class="btn btn-primary">
            Browse Recipes → <!-- Lien vers toutes les recettes -->
        </a>

        <a href="<?= SITE_URL ?>/index.php?page=add_recipe" class="btn">
            Share a Recipe <!-- Lien pour ajouter une recette -->
        </a>
    </div>
</section>

<!-- 🔥 POPULAR -->
<div class="section-heading">
    <h2>🔥 Most Popular</h2> <!-- Section recettes populaires -->
</div>

<?php if (!empty($popularRecipes)): ?>
    <div class="recipes-list">
        <?php foreach ($popularRecipes as $recipe): ?>
            <?php include 'components/recipe_card.php'; ?> <!-- Carte recette populaire -->
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- 🕒 LATEST -->
<div class="section-heading">
    <h2>🕒 Latest Recipes</h2> <!-- Section dernières recettes -->
</div>

<?php if (!empty($latestRecipes)): ?>
    <div class="recipes-list">
        <?php foreach ($latestRecipes as $recipe): ?>
            <?php include 'components/recipe_card.php'; ?> <!-- Carte recette récente -->
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include 'footer.php'; ?> <!-- Pied de page -->