<?php
$pageTitle = "Edit Profile"; // Titre page
require_once 'header.php'; // Inclut header
$user = $_SESSION['user']; // Récupère utilisateur connecté
?>

<div class="container"> <!-- Conteneur principal -->

    <div class="form-card"> <!-- Carte formulaire -->
        <h2>Edit Profile</h2> <!-- Titre -->

        <?php if (!empty($success)): ?>
            <p class="success">Profile updated ✅</p> <!-- Message succès -->
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <p class="error"><?= $error ?></p> <!-- Message erreur -->
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data"> <!-- Formulaire édition -->

            <label>Profile picture</label>
            <input type="file" name="avatar"> <!-- Upload avatar -->

            <label>Name *</label>
            <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required> <!-- Nom -->

            <label>Email *</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required> <!-- Email -->

            <label>Phone</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>"> <!-- Téléphone -->

            <button class="btn btn-primary">Save</button> <!-- Bouton sauvegarder -->
        </form>

        <a href="index.php?page=profile" class="btn">← Back</a> <!-- Bouton retour -->
    </div>

</div>

<?php require_once 'footer.php'; ?> <!-- Pied de page -->