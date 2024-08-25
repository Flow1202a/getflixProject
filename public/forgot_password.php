<?php
require_once('../includes/session.php'); // Assurez-vous que ce fichier est bien inclus et fonctionnel
require_once('../includes/db_connect.php'); // Assurez-vous que la connexion à la base de données est correcte

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../includes/PHPMailer/src/Exception.php';
require '../includes/PHPMailer/src/PHPMailer.php';
require '../includes/PHPMailer/src/SMTP.php';

function sendSecurityCode($email, $security_code): void
{
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'getflix650@gmail.com';
        $mail->Password = 'gzcy hplu shed ugoe';  // Remplacez par votre mot de passe d'application réel
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('getflix650@gmail.com', 'Your Site');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Votre code de sécurité';
        $mail->Body = "Votre code de sécurité est : $security_code";

        $mail->send();
    } catch (Exception $e) {
        echo "Le message n'a pas pu être envoyé. Erreur de Mailer : {$mail->ErrorInfo}";
    }
}

global $pdo;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    // Vérification de l'adresse email dans la base de données
    $query = "SELECT u.id, uc.security_question FROM users u
              JOIN user_conf uc ON u.id = uc.user_id
              WHERE u.email = :email";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $security_code = rand(0, 100);
        $query = "UPDATE user_conf SET security_code = :security_code WHERE user_id = :user_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':security_code' => $security_code,
            ':user_id' => $user['id']
        ]);

        // Envoyer le code de sécurité par email
        sendSecurityCode($email, $security_code);

        // Stocker l'ID utilisateur et la question de sécurité dans la session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['security_question'] = $user['security_question'];

        // Rediriger vers la page de question de sécurité
        header('Location: ../public/security_questions.php'); // Assurez-vous que le chemin est correct
        exit;
    } else {
        echo "Aucun utilisateur trouvé avec cet e-mail.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Récupération de mot de passe</title>
    <link rel="stylesheet" href="../style/registerLoginStyle.css">
</head>
<body>
<div class="wrapper">
    <header>Récupération de mot de passe</header>
    <form method="post" action="">
        <div class="input-box">
            <input type="email" id="email" name="email" placeholder="Adresse e-mail" required>
        </div>
        <button type="submit" class="btn">Suivant</button>
    </form>
</div>
</body>
</html>
