<?php
global $pdo;
require '../includes/PHPMailer/src/Exception.php';
require '../includes/PHPMailer/src/PHPMailer.php';
require '../includes/PHPMailer/src/SMTP.php';
require '../includes/db_connect.php'; // Inclusion du fichier de connexion PDO

function hashSecurityAnswer($answer): string
{
    return password_hash($answer, PASSWORD_BCRYPT);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $new_security_answer = $_POST['security_answer'];

    // Hachage de la réponse à la question de sécurité
    $hashed_answer = hashSecurityAnswer($new_security_answer);

    // Mise à jour de la réponse dans la base de données en utilisant PDO
    $stmt = $pdo->prepare("UPDATE user_conf SET security_answer = :security_answer WHERE user_id = :user_id");
    $stmt->execute(['security_answer' => $hashed_answer, 'user_id' => $user_id]);

    echo "La réponse à la question de sécurité a été mise à jour et hachée avec succès.";
}
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réinitialiser le Mot de Passe</title>
    <link rel="stylesheet" href="../style/registerLoginStyle.css">
</head>
<body>
<div class="wrapper">
    <header>Réinitialiser le Mot de Passe</header>
    <form method="post" action="">
        <div class="input-box">
            <input type="password" id="new_password" name="new_password" placeholder="Nouveau mot de passe" required>
        </div>
        <button type="submit" class="btn">Réinitialiser</button>
    </form>
</div>
</body>
</html>
