<?php
global $pdo;
session_start();
require_once('../includes/db_connect.php'); // Inclure le fichier de connexion à la base de données

$success_message = ''; // Initialize an empty success message
$error_message = '';   // Initialize an empty error message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];

    if ($password !== $confirm_password) {
        $error_message = "Passwords do not match. Please try again.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hacher le mot de passe

        try {
            // Insérer l'utilisateur dans la table users
            $stmt = $pdo->prepare("INSERT INTO users (username, email, created_at) VALUES (:username, :email, NOW())");
            $stmt->execute([
                ':username' => $username,
                ':email' => $email,
            ]);
            $user_id = $pdo->lastInsertId(); // Récupérer l'ID de l'utilisateur inséré

            // Insérer le mot de passe haché dans la table user_conf
            $stmt = $pdo->prepare("INSERT INTO user_conf (user_id, password) VALUES (:user_id, :password)");
            $stmt->execute([
                ':user_id' => $user_id,
                ':password' => $hashed_password
            ]);

            $success_message = "Registration successful!";
        } catch (PDOException $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    }
}
?>
?>
<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Register Form</title>
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
        <body>
            <div class="wrapper">
                <header>Sign Up</header>
                    <form method="post">
                        <?php if ($success_message): ?>
                            <div class="success-message">
                                <?php echo $success_message; ?>
                            </div>
                        <?php elseif ($error_message): ?>
                            <div class="error-message">
                                <?php echo $error_message; ?>
                            </div>
                        <?php endif; ?>

                        <div class="input-box">
                            <input type="text" name="username" id="username" placeholder="Username" required>
                        </div>
                        <div class="input-box">
                            <input type="email" name="email" id="email" placeholder="E-mail" required>
                        </div>
                        <div class="input-box">
                            <input type="password" name="password" id="password" placeholder="Password" required>
                        </div>
                        <div class="input-box">
                            <input type="password" name="confirm-password" id="confirm-password" placeholder="Confirm Password"
                                   required>
                        </div>
                        <input type="submit" name="submit" id="submit" value="REGISTER" class="btn">
                    </form>
                <div class="register-link">
                    <p>Already have an account? <a href="../public/logintest.php">Sign In</a></p>
                </div>
            </div>
        </body>
</html>

