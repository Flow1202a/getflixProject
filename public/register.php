<?php
global $pdo;
session_start();
require_once('../includes/db_connect.php'); // Inclure le fichier de connexion à la base de données

$success_message = ''; // Initialiser un message de succès vide
$error_message = '';   // Initialiser un message d'erreur vide

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];
    $security_question = $_POST['security_question'];
    $security_answer = $_POST['security_answer'];

    if ($password !== $confirm_password) {
        $error_message = "Les mots de passe ne correspondent pas. Veuillez réessayer.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hacher le mot de passe
        $hashed_security_answer = password_hash($security_answer, PASSWORD_DEFAULT); // Hacher la réponse à la question de sécurité

        try {
            // Insérer l'utilisateur dans la table users
            $stmt = $pdo->prepare("INSERT INTO users (username, email, role, created_at) VALUES (:username, :email, 'user', NOW())");
            $stmt->execute([
                ':username' => $username,
                ':email' => $email,
            ]);
            $user_id = $pdo->lastInsertId(); // Récupérer l'ID de l'utilisateur inséré

            // Insérer le mot de passe haché et les informations de sécurité dans la table user_conf
            $stmt = $pdo->prepare("INSERT INTO user_conf (user_id, password, security_question, security_answer) VALUES (:user_id, :password, :security_question, :security_answer)");
            $stmt->execute([
                ':user_id' => $user_id,
                ':password' => $hashed_password,
                ':security_question' => $security_question,
                ':security_answer' => $hashed_security_answer
            ]);

            $success_message = "Inscription réussie !";
        } catch (PDOException $e) {
            $error_message = "Erreur : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire d'inscription</title>
    <link rel="stylesheet" href="../style/registerLoginStyle.css">
</head>
<body>
<div class="wrapper">
    <header>Inscription</header>
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
            <input type="text" name="username" id="username" placeholder="Nom d'utilisateur" required>
        </div>
        <div class="input-box">
            <input type="email" name="email" id="email" placeholder="E-mail" required>
        </div>
        <div class="input-box">
            <input type="password" name="password" id="password" placeholder="Mot de passe" required>
        </div>
        <div class="input-box">
            <input type="password" name="confirm-password" id="confirm-password" placeholder="Confirmez le mot de passe" required>
        </div>
        <div class="input-box">
            <select class="selectionBox" name="security_question" id="security_question" required>
                <option value="" disabled selected>Choisissez une question de sécurité</option>
                <option value="Quel est le nom de votre premier animal de compagnie ?">Quel est le nom de votre premier animal de compagnie ?</option>
                <option value="Quel est le nom de jeune fille de votre mère ?">Quel est le nom de jeune fille de votre mère ?</option>
                <option value="Quel est le nom de votre école primaire ?">Quel est le nom de votre école primaire ?</option>
                <option value="Quel est le nom de la ville où vous êtes né(e) ?">Quel est le nom de la ville où vous êtes né(e) ?</option>
            </select>
        </div>
        <div class="input-box">
            <input type="text" name="security_answer" id="security_answer" placeholder="Réponse à la question de sécurité" required>
        </div>
        <input type="submit" name="submit" id="submit" value="S'inscrire" class="btn">
    </form>
    <div class="register-link">
        <p>Vous avez déjà un compte ? <a href="../public/logintest.php">Se connecter</a></p>
    </div>
</div>
</body>
</html>
