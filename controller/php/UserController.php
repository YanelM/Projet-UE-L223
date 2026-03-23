<?php
require_once(__DIR__ . "/../../model/php/UserModel.php");

// Contrôleur pour gérer les utilisateurs
class UserController {

    // Connexion utilisateur
    public function login() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $error = '';
        $success = $_GET['success'] ?? false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (!$email || !$password) {
                $error = 'Please enter both email and password'; // vérifie les champs
            } else {
                $user = User::verify($email, $password);          // vérifie credentials
                if ($user) {
                    $_SESSION['user'] = $user;                   // sauvegarde session
                    header("Location: index.php?page=recipes"); // redirige
                    exit;
                } else {
                    $error = 'Invalid credentials';
                }
            }
        }

        include(__DIR__ . "/../../view/php/login.php"); // affiche le formulaire
    }

    // Inscription utilisateur
    public function register() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            // validation simple
            if (!$username || !$email || !$password) {
                $error = 'All fields are required';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Invalid email format';
            } elseif (User::findByEmail($email)) {
                $error = 'Email already exists';
            } else {
                User::create($username, $email, $password); // crée l'utilisateur
                header("Location: index.php?page=login&success=1"); // redirige login
                exit;
            }
        }

        include(__DIR__ . "/../../view/php/register.php"); // affiche formulaire
    }

    // Déconnexion
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_destroy(); // détruit session
        header("Location: index.php"); // redirige accueil
        exit;
    }

    // Profil personnel
    public function profile() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?page=login"); // redirige si non connecté
            exit;
        }
        include(__DIR__ . "/../../view/php/profile.php"); // affiche profil
    }

    // Affiche recettes liées au profil
    public function profileRecipes() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $user = $_SESSION['user'] ?? null;
        if (!$user) header("Location: index.php?page=login");

        $type = $_GET['type'] ?? 'favorites';

        switch($type) {
            case 'my_recipes':
                $recipes = Recipe::byUser($user['id']); // mes recettes
                $title = "My Recipes";
                break;
            case 'history':
                $recipes = Recipe::history($user['id']); // historique
                $title = "History";
                break;
            case 'favorites':
            default:
                $recipes = Recipe::favorites($user['id']); // favoris
                $title = "Favorites";
                break;
        }

        include(__DIR__ . "/../../view/php/profile_recipes.php");
    }

    // Modifier profil
    public function editProfile() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $user = $_SESSION['user'] ?? null;
        if (!$user) header("Location: index.php?page=login");

        $error = '';
        $success = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $email    = trim($_POST['email']);
            $phone    = trim($_POST['phone']);

            if (!$username || !$email) $error = "Name and email are required.";
            elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $error = "Invalid email.";
            else {
                $avatarPath = null;

                // upload avatar
                if (!empty($_FILES['avatar']['name'])) {
                    $uploadDir = 'uploads/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir);
                    $filename = time() . '_' . basename($_FILES['avatar']['name']);
                    $target = $uploadDir . $filename;
                    move_uploaded_file($_FILES['avatar']['tmp_name'], $target);
                    $avatarPath = $target;
                }

                User::updateFull($user['id'], $username, $email, $phone, $avatarPath); // update DB

                // update session
                $_SESSION['user']['username'] = $username;
                $_SESSION['user']['email'] = $email;
                $_SESSION['user']['phone'] = $phone;
                if ($avatarPath) $_SESSION['user']['avatar'] = $avatarPath;

                $success = true;
            }
        }

        include(__DIR__ . "/../../view/php/edit_profile.php");
    }

    // Profil public d'un autre utilisateur
    public function publicProfile() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $id = $_GET['id'] ?? null;
        if (!$id) header("Location: index.php");

        $user = User::findById($id);
        if (!$user) { echo "User not found"; exit; }

        $recipes = Recipe::byUser($id);              // recettes
        $likesCount = User::totalLikes($id);         // nombre de likes
        $isLiked = isset($_SESSION['user']) && User::isLiked($_SESSION['user']['id'], $id);

        include(__DIR__ . "/../../view/php/public_profile.php");
    }

    // Ajouter/retirer like sur un utilisateur
    public function toggleLike() {
        if (!isset($_SESSION['user'])) header("Location: index.php?page=login");

        $user_id = $_SESSION['user']['id'];
        $target_user_id = $_GET['id'] ?? 0;

        if (!$target_user_id || $target_user_id == $user_id)
            header("Location: index.php?page=recipes");

        if (User::isLiked($user_id, $target_user_id))
            User::removeLike($user_id, $target_user_id); // retire like
        else
            User::addLike($user_id, $target_user_id);    // ajoute like

        header("Location: index.php?page=public_profile&id=$target_user_id");
        exit;
    }
}