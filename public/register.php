<?php
    global $pdo;
    session_start();
    require_once('../includes/db_connect.php'); // Inclure le fichier de connexion à la base de données

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
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

            echo "Inscription réussie.";
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Form</title>
    <link rel="stylesheet" href="../style/registerLoginStyle.css">
</head>
    <body>
        <div class="wrapper">
            <header>Sign Up</header>
            <form method="post">
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
                    <input type="password" name="confirm-password" id="confirm-password" placeholder="Confirm Password" required>
                </div>
                <input type="submit" name="submit" id="submit" value="REGISTER" class="btn">
            </form>
            <div class="register-link">
                <p>Already have an account? <a href="../public/login.php">Sign In</a></p>
            </div>
        </div>
    </body>
</html>

