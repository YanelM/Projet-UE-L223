<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8"> <!-- Encodage UTF-8 -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsive -->

        <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : "Cook n' Share" ?></title> <!-- Titre page -->

        <link rel="preconnect" href="https://fonts.googleapis.com"> <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin> <!-- Préconnect -->

        <link rel="stylesheet" href="<?= SITE_URL ?>/view/assets/css/style.css"> <!-- CSS principal -->
    </head>

    <body>

        <nav class="navbar">

            <a href="<?= SITE_URL ?>/index.php" class="logo">
            🍳 Cook n' Share <!-- Logo / titre site -->
            </a>

            <div class="nav-links">
                <a href="<?= SITE_URL ?>/index.php">Home</a> <!-- Accueil -->
                <a href="<?= SITE_URL ?>/index.php?page=recipes">Browse</a> <!-- Liste recettes -->
                <a href="<?= SITE_URL ?>/index.php?page=add_recipe">Add Recipe</a> <!-- Ajouter recette -->

                <?php if (isset($_SESSION['user'])): ?>
                    <a href="<?= SITE_URL ?>/index.php?page=profile">Profile</a> <!-- Profil connecté -->
                <?php else: ?>
                    <a href="<?= SITE_URL ?>/index.php?page=login">Login</a> <!-- Connexion -->
                    <a href="<?= SITE_URL ?>/index.php?page=register" class="btn-nav">Sign Up</a> <!-- Inscription -->
                <?php endif; ?>
            </div>

        </nav>

        <div class="container"> <!-- Conteneur principal -->