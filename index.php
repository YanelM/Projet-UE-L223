<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require 'model/php/Database.php';
require 'model/php/UserModel.php';
require 'model/php/RecipeModel.php';
require 'controller/php/UserController.php';
require 'controller/php/RecipeController.php';
require 'model/php/CommentModel.php';
require 'controller/php/CommentController.php';

$page = $_GET['page'] ?? 'home';

switch($page) {
    case 'login':
        $controller = new UserController();
        $controller->login();
        break;
    case 'register':
        $controller = new UserController();
        $controller->register();
        break;
    case 'logout':
        $controller = new UserController();
        $controller->logout();
        break;
    case 'add_recipe':
        $controller = new RecipeController();
        $controller->add();
        break;
    case 'recipe':
        $controller = new RecipeController();
        $controller->view();
        break;
    case 'recipes':
        $controller = new RecipeController();
        $controller->list();
        break;
    case 'profile':
        $controller = new UserController();
        $controller->profile();
        break;
    case 'edit_recipe':
        $controller = new RecipeController();
        $controller->edit();
        break;
    case 'edit_profile':
        $controller = new UserController();
        $controller->editProfile();
        break;
    case 'profile_recipes':
        $controller = new UserController();
        $controller->profileRecipes();
        break;
    case 'delete_recipe':
        $controller = new RecipeController();
        $controller->delete();
        break;
    case 'toggle_favorite':
        $controller = new RecipeController();
        $controller->toggleFavorite();
        break;
    case 'add_comment':
        $controller = new CommentController();
        $controller->add();
        break;

    // ===== NOUVELLES ROUTES =====
    case 'public_profile':
        $controller = new UserController();
        $controller->publicProfile();
        break;
    case 'toggleLike':
        $controller = new UserController();
        $controller->toggleLike();
        break;

    default:
        require_once 'model/php/RecipeModel.php';
        $recipes = Recipe::latest(5);
        include 'view/php/home.php';
}