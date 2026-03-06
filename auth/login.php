<?php
require_once '../includes/config.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: " . SITE_URL . "/index.php");
    exit;
}

$pageTitle = "Login — Cook n' Share";
$error = '';
$successMsg = '';

if (isset($_GET['registered'])) {
    $successMsg = "Account created! You can now log in.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $db   = getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: " . SITE_URL . "/index.php");
            exit;
        } else {
            $error = "Invalid email or password.";
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
        <h2>Welcome back</h2>
        <p>Sign in to your Cook n' Share account.</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($successMsg): ?>
            <div class="alert alert-success"><?= htmlspecialchars($successMsg) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" id="email" name="email" required
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                    placeholder="you@example.com">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="••••••••">
            </div>

            <button type="submit" class="btn btn-primary btn-block">Sign in →</button>
        </form>

        <p class="form-footer">
            Don't have an account? <a href="register.php">Create one</a>
        </p>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
