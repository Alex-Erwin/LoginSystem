<?php
session_start();


if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_type'] === 'admin') {
        header('Location: admin.php');
    } else {
        header('Location: booking.php');
    }
    exit;
}

require 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_type'] = $user['type'];
        $_SESSION['user_name'] = $user['first_name'];

        if ($user['type'] === 'admin') {
            header('Location: admin.php');
        } else {
            header('Location: booking.php');
        }
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Fine Lines Lawn Care</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.html">Fine Lines Lawn Care</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item"><a class="nav-link" href="index.html">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="lawn-mowing.html">Lawn Mowing</a></li>
                <li class="nav-item"><a class="nav-link" href="hedge-trimming.html">Hedge Trimming</a></li>
                <li class="nav-item"><a class="nav-link" href="snow-plowing.html">Snow Plowing</a></li>
                <li class="nav-item"><a class="nav-link" href="booking.php">Book Now</a></li>
                <li class="nav-item ms-lg-2"><a class="btn btn-success btn-sm" href="login.php">Login</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container my-5">
    <div class="row">
        <div class="col-md-5 mx-auto">
            <h2 class="text-center mb-4">Login</h2>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>

            <p class="text-center mt-3">Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </div>
</div>

<footer class="bg-dark text-white text-center p-4">
    <p>Fine Lines Lawn Care | Barre, Vermont</p>
    <p class="mb-0">&copy; 2026 Fine Lines Lawn Care</p>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
