<?php
session_start(); // Démarre la session

// Import des fichiers nécessaires
require 'model/php/Database.php';
require 'model/php/UserModel.php';
require 'model/php/RecipeModel.php';
require 'controller/php/UserController.php';
require 'controller/php/RecipeController.php';
require 'model/php/CommentModel.php';
require 'controller/php/CommentController.php';

// Récupère la page demandée ou 'home' par défaut
$page = $_GET['page'] ?? 'home';

// Gestion des différentes pages
switch($page) {
    case 'login':
        $controller = new UserController(); // Crée le contrôleur utilisateur
        $controller->login(); // Appelle la fonction login
        break;
    case 'register':
        $controller = new UserController();
        $controller->register(); // Page d'inscription
        break;
    case 'logout':
        $controller = new UserController();
        $controller->logout(); // Déconnexion
        break;
    case 'add_recipe':
        $controller = new RecipeController();
        $controller->add(); // Ajouter une recette
        break;
    case 'recipe':
        $controller = new RecipeController();
        $controller->view(); // Voir une recette
        break;
    case 'recipes':
        $controller = new RecipeController();
        $controller->list(); // Liste de recettes
        break;
    case 'profile':
        $controller = new UserController();
        $controller->profile(); // Profil utilisateur
        break;
    case 'edit_recipe':
        $controller = new RecipeController();
        $controller->edit(); // Modifier une recette
        break;
    case 'edit_profile':
        $controller = new UserController();
        $controller->editProfile(); // Modifier le profil
        break;
    case 'profile_recipes':
        $controller = new UserController();
        $controller->profileRecipes(); // Recettes de l'utilisateur
        break;
    case 'delete_recipe':
        $controller = new RecipeController();
        $controller->delete(); // Supprimer une recette
        break;
    case 'toggle_favorite':
        $controller = new RecipeController();
        $controller->toggleFavorite(); // Ajouter/supprimer des favoris
        break;
    case 'add_comment':
        $controller = new CommentController();
        $controller->add(); // Ajouter un commentaire
        break;
    case 'public_profile':
        $controller = new UserController();
        $controller->publicProfile(); // Profil public
        break;
    case 'toggleLike':
        $controller = new UserController();
        $controller->toggleLike(); // Like/Dislike
        break;
    default:
        require_once 'model/php/RecipeModel.php';
        $recipes = Recipe::latest(5); // Récupère les 5 dernières recettes
        include 'view/php/home.php'; // Affiche la page d'accueil
}