<?php
$pageTitle = "Register — Cook n' Share"; // Titre de la page
require_once 'header.php'; // Inclut l'en-tête
?>

<div class="form-page">
    <div class="form-card">
        <h2>Create account</h2> <!-- Titre du formulaire -->
        <p>Join Cook n' Share and start sharing your recipes.</p> <!-- Description -->

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div> <!-- Affiche l'erreur -->
        <?php endif; ?>

        <form method="POST"> <!-- Formulaire d'inscription -->
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required
                    value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" 
                    placeholder="chefmario"> <!-- Exemple de pseudo -->
            </div>

            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" id="email" name="email" required
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" 
                    placeholder="you@example.com"> <!-- Exemple d'email -->
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Choose a strong password"> <!-- Mot de passe -->
            </div>

            <button type="submit" class="btn btn-primary btn-block">Create account →</button> <!-- Bouton soumettre -->
        </form>

        <p class="form-footer">
            Already have an account?<a href="<?= SITE_URL ?>/index.php?page=login">Sign in</a> <!-- Lien vers login -->
        </p>
    </div>
</div>

<?php require_once 'footer.php'; ?> <!-- Inclut le pied de page -->