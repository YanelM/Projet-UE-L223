<?php
require_once '../includes/config.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/index.php');
    exit;
}

$pageTitle = "Register — Cook n' Share";
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $db    = getDB();
        $check = $db->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);

        if ($check->fetch()) {
            $error = "That email address is already registered.";
        } else {
            $stmt = $db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$username, $email, password_hash($password, PASSWORD_DEFAULT)]);
            header("Location: login.php?registered=1");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="<?= SITE_URL ?>/css/style.css">
</head>
<body>

<?php require_once '../includes/header.php'; ?>

<div class="form-page">
    <div class="form-card">
        <h2>Create account</h2>
        <p>Join Cook n' Share and start sharing your recipes.</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required
                    value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                    placeholder="chefmario">
            </div>

            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" id="email" name="email" required
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                    placeholder="you@example.com">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Choose a strong password">
            </div>

            <button type="submit" class="btn btn-primary btn-block">Create account →</button>
        </form>

        <p class="form-footer">
            Already have an account? <a href="login.php">Sign in</a>
        </p>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
