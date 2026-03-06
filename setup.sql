CREATE DATABASE cooknshare;
USE cooknshare;

-- USERS TABLE
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- RECIPES TABLE
CREATE TABLE recipes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    category VARCHAR(100),
    difficulty VARCHAR(50),
    prep_time INT DEFAULT 0,
    cook_time INT DEFAULT 0,
    servings INT DEFAULT NULL,
    ingredients TEXT,
    instructions TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- SAMPLE USER
INSERT INTO users (username, email, password)
VALUES ('chefadmin', 'admin@test.com', '123456');

-- SAMPLE RECIPES
INSERT INTO recipes (user_id, title, description, category, difficulty, prep_time, cook_time, servings)
VALUES
(1, 'Classic Spaghetti', 'Simple Italian pasta dish.', 'Pasta', 'Easy', 10, 15, 2),
(1, 'Chocolate Cake', 'Rich homemade chocolate cake.', 'Dessert', 'Medium', 20, 35, 6),
(1, 'Vegetable Soup', 'Healthy homemade soup.', 'Soup', 'Easy', 15, 25, 4);