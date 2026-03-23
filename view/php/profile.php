<?php
$pageTitle = "Profile"; // Titre de la page
require_once 'header.php'; // Inclut l'en-tête
$user = $_SESSION['user']; // Récupère l'utilisateur connecté
?>

<div class="container">

    <div class="profile-header-card">

        <div class="avatar">
            <?php if (!empty($user['avatar'])): ?>
                <img src="<?= SITE_URL . $user['avatar'] ?>" class="avatar-img"> <!-- Avatar utilisateur -->
            <?php else: ?>
                👩‍🍳 <!-- Icône par défaut -->
            <?php endif; ?>
        </div>

        <h2><?= htmlspecialchars($user['username']) ?></h2> <!-- Nom utilisateur -->
        <p><?= htmlspecialchars($user['email']) ?></p> <!-- Email utilisateur -->

    </div>

    <!-- MENU -->
    <div class="profile-menu-vertical">

        <a href="index.php?page=profile_recipes&type=favorites" class="profile-item">
            ❤️ Favorites <!-- Recettes favorites -->
        </a>

        <a href="index.php?page=profile_recipes&type=my_recipes" class="profile-item">
            📖 My recipes <!-- Mes recettes -->
        </a>

        <a href="index.php?page=profile_recipes&type=history" class="profile-item">
            🕒 History <!-- Historique -->
        </a>

    </div>

    <!-- ACTIONS -->
    <div class="profile-actions">
        <a href="index.php?page=edit_profile" class="btn btn-primary">Edit</a> <!-- Modifier profil -->
        <a href="index.php?page=logout" class="btn btn-danger">Log out</a> <!-- Déconnexion -->
    </div>

</div>

<?php require_once 'footer.php'; ?> <!-- Pied de page -->