<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : "Cook n' Share" ?></title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link rel="stylesheet" href="<?= defined('SITE_URL') ? SITE_URL : '' ?>/css/style.css">
</head>

<body>

<nav class="navbar">

<a href="<?= SITE_URL ?>/index.php" class="logo">
🍳 Cook n' Share
</a>

<div class="nav-links">

<a href="<?= SITE_URL ?>/index.php">Home</a>

<a href="<?= SITE_URL ?>/recipes/recipes.php">Browse</a>

<a href="<?= SITE_URL ?>/recipes/add_recipe.php">Add Recipe</a>

<?php if (isset($_SESSION['user_id'])): ?>

<a href="<?= SITE_URL ?>/auth/logout.php">Log out</a>

<?php else: ?>

<a href="<?= SITE_URL ?>/auth/login.php">Login</a>

<a href="<?= SITE_URL ?>/auth/register.php" class="btn-nav">Sign Up</a>

<?php endif; ?>

</div>

</nav>

<div class="container">