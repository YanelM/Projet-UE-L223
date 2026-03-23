-- ============================================================
-- USERS TABLE
-- ============================================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,       -- identifiant unique de l'utilisateur
    username VARCHAR(50) NOT NULL UNIQUE,    -- nom d'utilisateur, unique et obligatoire
    email VARCHAR(100) NOT NULL UNIQUE,      -- email, unique et obligatoire
    password VARCHAR(255) NOT NULL,          -- mot de passe hashé
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- date de création
);

-- Ajout de champs optionnels pour le profil
ALTER TABLE users 
ADD phone VARCHAR(20) NULL,    -- numéro de téléphone optionnel
ADD avatar VARCHAR(255) NULL;  -- photo de profil optionnelle

-- ============================================================
-- RECIPES TABLE
-- ============================================================
CREATE TABLE recipes (
    id INT AUTO_INCREMENT PRIMARY KEY,       -- identifiant unique de la recette
    user_id INT NOT NULL,                    -- référence à l'auteur (users.id)
    title VARCHAR(255) NOT NULL,             -- titre de la recette
    description TEXT,                        -- description de la recette
    category VARCHAR(100),                   -- catégorie (ex: Pasta, Dessert)
    difficulty VARCHAR(50),                  -- difficulté (Easy, Medium, Hard)
    prep_time INT DEFAULT 0,                 -- temps de préparation (minutes)
    cook_time INT DEFAULT 0,                 -- temps de cuisson (minutes)
    servings INT DEFAULT NULL,               -- nombre de portions
    ingredients TEXT,                        -- liste des ingrédients
    instructions TEXT,                       -- liste des instructions
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- date de création
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE -- suppression en cascade
);

-- Ajouter un champ image pour la recette
ALTER TABLE recipes ADD image VARCHAR(255) NULL;

-- ============================================================
-- COMMENTS TABLE
-- ============================================================
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,       -- identifiant unique du commentaire
    user_id INT NOT NULL,                    -- auteur du commentaire
    recipe_id INT NOT NULL,                  -- recette associée
    content TEXT NOT NULL,                   -- contenu du commentaire
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- date de création
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE
);

-- Ajout du support pour les réponses aux commentaires
ALTER TABLE comments 
ADD parent_id INT NULL,                      -- permet un commentaire parent
ADD FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE;

-- ============================================================
-- FAVORITES TABLE
-- ============================================================
CREATE TABLE recipe_favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,       -- identifiant unique
    user_id INT NOT NULL,                    -- utilisateur qui a favorisé
    recipe_id INT NOT NULL,                  -- recette favorisée
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- date de l'action
    UNIQUE KEY(user_id, recipe_id),          -- un utilisateur ne peut pas favori la même recette 2 fois
    FOREIGN KEY(user_id) REFERENCES users(id),
    FOREIGN KEY(recipe_id) REFERENCES recipes(id)
);

-- ============================================================
-- VIEWS TABLE
-- ============================================================
CREATE TABLE recipe_views (
    id INT AUTO_INCREMENT PRIMARY KEY,       -- identifiant unique
    user_id INT NOT NULL,                    -- utilisateur qui a vu
    recipe_id INT NOT NULL,                  -- recette vue
    viewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- date de la vue
    FOREIGN KEY(user_id) REFERENCES users(id),
    FOREIGN KEY(recipe_id) REFERENCES recipes(id)
);

-- ============================================================
-- USER LIKES (suivi des likes entre utilisateurs)
-- ============================================================
CREATE TABLE user_likes (
    id INT AUTO_INCREMENT PRIMARY KEY,       -- identifiant unique
    user_id INT NOT NULL,                    -- utilisateur qui like
    target_user_id INT NOT NULL,             -- utilisateur liké
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- date
    UNIQUE(user_id, target_user_id),         -- pas de double like
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (target_user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================================
-- SAMPLE DATA
-- ============================================================

-- Création d'un utilisateur exemple
INSERT INTO users (username, email, password)
VALUES ('chefadmin', 'admin@test.com', '123456');

-- Création de recettes exemples pour l'utilisateur 1
INSERT INTO recipes (user_id, title, description, category, difficulty, prep_time, cook_time, servings)
VALUES
(1, 'Classic Spaghetti', 'Simple Italian pasta dish.', 'Pasta', 'Easy', 10, 15, 2),
(1, 'Chocolate Cake', 'Rich homemade chocolate cake.', 'Dessert', 'Medium', 20, 35, 6),
(1, 'Vegetable Soup', 'Healthy homemade soup.', 'Soup', 'Easy', 15, 25, 4);