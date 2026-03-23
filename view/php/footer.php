    </div><!-- /.container --> <!-- Fin conteneur principal -->
    <?php $page = $_GET['page'] ?? 'home'; ?> <!-- Page actuelle -->

    <div class="bottom-nav"> <!-- Navigation inférieure -->

        <!-- HOME -->
        <a href="<?= SITE_URL ?>/index.php"
        class="nav-item <?= $page === 'home' ? 'active' : '' ?>"> <!-- Lien accueil -->
            <span>🏠</span>
            <small>Home</small>
        </a>

        <!-- SEARCH -->
        <a href="<?= SITE_URL ?>/index.php?page=recipes"
        class="nav-item <?= $page === 'recipes' ? 'active' : '' ?>"> <!-- Lien recherche -->
            <span>🔍</span>
            <small>Search</small>
        </a>

        <!-- ADD (visible seulement connecté) -->
        <?php if (isset($_SESSION['user'])): ?>
            <a href="<?= SITE_URL ?>/index.php?page=add_recipe"
            class="nav-item <?= $page === 'add_recipe' ? 'active' : '' ?>"> <!-- Ajouter recette -->
                <span>➕</span>
                <small>Add</small>
            </a>
        <?php endif; ?>

        <!-- PROFILE / LOGIN -->
        <a href="<?= SITE_URL ?>/index.php?page=<?= isset($_SESSION['user']) ? 'profile' : 'login' ?>"
        class="nav-item <?= ($page === 'profile' || $page === 'login') ? 'active' : '' ?>"> <!-- Profil ou login -->
            <span>👤</span>
            <small><?= isset($_SESSION['user']) ? 'Profile' : 'Login' ?></small>
        </a>

    </div>

</body>
</html> <!-- Fin page -->