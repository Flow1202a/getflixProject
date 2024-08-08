<?php

global $pdo;
require_once('../includes/db_connect.php');

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

                header("Location: admin.php");
                exit;  // Prevent further script execution
            } else {
                // Login failed - avoid specific error messages for security
                echo "Invalid login credentials.";
            }
        } catch(PDOException $e) {
            // Handle database errors gracefully
            echo "Database error: " . $e->getMessage();
        }
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="../style/registerLoginStyle.css">
</head>
<body>
    <div class="wrapper">
        <header>Sign In</header>
        <form method="post">
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
</body>
</html>