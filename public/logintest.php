<?php

global $pdo;
require_once('../includes/db_connect.php');

$error_message = '';  // Initialize an empty error message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);  // Sanitize username
    $password = $_POST['password'];

    try {
        // Prepare a secure SQL statement
        $sql = "SELECT users.id, users.username, users.email, users.role, user_conf.password
                FROM users
                INNER JOIN user_conf ON users.id = user_conf.user_id
                WHERE users.username = :username OR users.email = :username";
        $stmt = $pdo->prepare($sql);

        // Bind parameters to prevent SQL injection
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);

        // Execute the prepared statement
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Login successful
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            header("Location: ../includes/back_office.php");
            exit;  // Prevent further script execution
        } else {
            // Login failed - avoid specific error messages for security
            $error_message = "Invalid login credentials. Please try again.";
        }
    } catch(PDOException $e) {
        // Handle database errors gracefully
        $error_message = "Database error: " . $e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css" />
    <link rel="stylesheet" href="../style/registerLoginStyle.css">
    <link rel="stylesheet" href="../style/style.css">

</head>
<body>
<header>
    <nav class="nav position-absolute">
        <i class="uil uil-bars navOpenBtn"></i>
        <a href="#" class="logo">GetFlixDeNullos</a>

        <ul class="nav-links align-items-center">
            <i class="uil uil-times navCloseBtn"></i>
            <li><a href="index.php">Home</a></li>
            <li><a href="#">Categories</a></li>
            <li><a href="#">WatchList</a></li>
            <li><a href="../includes/back_office.php">Account</a></li>
            <li><a href="logintest.php">Connexion</a></li>
        </ul>

        <i class="uil uil-search search-icon" id="searchIcon"></i>
        <div class="search-box">
            <i class="uil uil-search search-icon"></i>
            <input type="text" placeholder="Search here..." />
        </div>
    </nav>
</header>

<div class="wrapper">
    <header>Sign In</header>
    <form method="post">
        <?php if ($error_message): ?>
            <div class="error-message">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <div class="input-box">
            <input type="text" placeholder="Username" name="username" required>
            <box-icon type='solid' name='user' color="white"></box-icon>
        </div>
        <div class="input-box">
            <input type="password" placeholder="Password" name="password" required>
            <box-icon name='lock-alt' type='solid' color="white"></box-icon>
        </div>

        <div class="remember-forget">
            <a href="#">Forgot password?</a>
        </div>
        <button type="submit" class="btn">Login</button>

        <div class="register-link">
            <p>Don't have an account?<br> <a href="../public/register.php">Sign Up</a></p>
        </div>
    </form>
</div>



<script src="../javascript/scriptMovie.js"></script>
</body>
</html>
