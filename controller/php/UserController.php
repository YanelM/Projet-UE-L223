<?php
require_once(__DIR__ . "/../../model/php/UserModel.php");

class UserController {

    public function login() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $error = '';
        $success = $_GET['success'] ?? false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (!$email || !$password) {
                $error = 'Please enter both email and password';
            } else {
                $user = User::verify($email, $password);
                if ($user) {
                    $_SESSION['user'] = $user;
                    header("Location: index.php?page=recipes");
                    exit;
                } else {
                    $error = 'Invalid credentials';
                }
            }
        }

        include(__DIR__ . "/../../view/php/login.php");
    }

    public function register() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (!$username || !$email || !$password) {
                $error = 'All fields are required';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Invalid email format';
            } elseif (User::findByEmail($email)) {
                $error = 'Email already exists';
            } else {
                User::create($username, $email, $password);
                header("Location: index.php?page=login&success=1");
                exit;
            }
        }

        include(__DIR__ . "/../../view/php/register.php");
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_destroy();
        header("Location: index.php");
        exit;
    }

    public function profile() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['user'])) {
            header("Location: index.php?page=login");
            exit;
        }

        include(__DIR__ . "/../../view/php/profile.php");
    }

    public function profileRecipes() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $user = $_SESSION['user'] ?? null;

        if (!$user) {
            header("Location: index.php?page=login");
            exit;
        }

        $type = $_GET['type'] ?? 'favorites';

        switch($type) {
            case 'my_recipes':
                $recipes = Recipe::byUser($user['id']);
                $title = "My Recipes";
                break;

            case 'history':
                $recipes = Recipe::history($user['id']);
                $title = "History";
                break;

            case 'favorites':
            default:
                $recipes = Recipe::favorites($user['id']);
                $title = "Favorites";
                break;
        }

        include(__DIR__ . "/../../view/php/profile_recipes.php");
    }

    public function editProfile() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $user = $_SESSION['user'] ?? null;

        if (!$user) {
            header("Location: index.php?page=login");
            exit;
        }

        $error = '';
        $success = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $email    = trim($_POST['email']);
            $phone    = trim($_POST['phone']);

            if (!$username || !$email) {
                $error = "Name and email are required.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Invalid email.";
            } else {

                $avatarPath = null;

                // upload image
                if (!empty($_FILES['avatar']['name'])) {
                    $uploadDir = 'uploads/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir);

                    $filename = time() . '_' . basename($_FILES['avatar']['name']);
                    $target = $uploadDir . $filename;

                    move_uploaded_file($_FILES['avatar']['tmp_name'], $target);
                    $avatarPath = $target;
                }

                User::updateFull($user['id'], $username, $email, $phone, $avatarPath);

                // update session
                $_SESSION['user']['username'] = $username;
                $_SESSION['user']['email'] = $email;
                $_SESSION['user']['phone'] = $phone;

                if ($avatarPath) {
                    $_SESSION['user']['avatar'] = $avatarPath;
                }

                $success = true;
            }
        }

        include(__DIR__ . "/../../view/php/edit_profile.php");
    }

    public function publicProfile() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $id = $_GET['id'] ?? null;

        if (!$id) {
            header("Location: index.php");
            exit;
        }

        $user = User::findById($id);

        if (!$user) {
            echo "User not found";
            exit;
        }

        // recettes de cet utilisateur
        $recipes = Recipe::byUser($id);

        // likes
        $likesCount = User::totalLikes($id);

        $isLiked = false;
        if (isset($_SESSION['user'])) {
            $isLiked = User::isLiked($_SESSION['user']['id'], $id);
        }

        include(__DIR__ . "/../../view/php/public_profile.php");
    }

    public function toggleLike() {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?page=login");
            exit;
        }

        $user_id = $_SESSION['user']['id'];
        $target_user_id = $_GET['id'] ?? 0;

        if (!$target_user_id || $target_user_id == $user_id) {
            header("Location: index.php?page=recipes");
            exit;
        }

        if (User::isLiked($user_id, $target_user_id)) {
            User::removeLike($user_id, $target_user_id);
        } else {
            User::addLike($user_id, $target_user_id);
        }

        // Redirection vers le profil public
        header("Location: index.php?page=public_profile&id=$target_user_id");
        exit;
    }
}