<?php
$pageTitle = "Login — Cook n' Share"; // Titre de la page
require_once 'header.php'; // Inclut l'en-tête
?>

<div class="form-page">
    <div class="form-card">
        <h2>Welcome back</h2> <!-- Titre du formulaire -->
        <p>Sign in to your Cook n' Share account.</p> <!-- Sous-titre -->

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div> <!-- Message erreur -->
        <?php endif; ?>

        <?php if (!empty($_GET['success'])): ?>
            <div class="alert alert-success">Account created! You can now log in.</div> <!-- Message succès -->
        <?php endif; ?>

        <form method="POST" action="<?= SITE_URL ?>/index.php?page=login"> <!-- Formulaire login -->
            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" id="email" name="email" required
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                    placeholder="you@example.com"> <!-- Email -->
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="••••••••"> <!-- Mot de passe -->
            </div>

            <button type="submit" class="btn btn-primary btn-block">Sign in →</button> <!-- Bouton submit -->
        </form>

        <p class="form-footer">
            Don't have an account? <a href="<?= SITE_URL ?>/index.php?page=register">Create one</a> <!-- Lien vers inscription -->
        </p>
    </div>
</div>

<?php require_once 'footer.php'; ?> <!-- Pied de page -->