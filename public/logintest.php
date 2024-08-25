<?php
global $pdo;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once('../includes/db_connect.php');
require '../includes/PHPMailer/src/Exception.php';
require '../includes/PHPMailer/src/PHPMailer.php';
require '../includes/PHPMailer/src/SMTP.php';

// Fonction pour envoyer le code de sécurité par e-mail
function sendSecurityCode($email, $security_code): void
{
    $mail = new PHPMailer(true);
    try {
        // Configuration du serveur SMTP pour envoyer l'e-mail
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'getflix650@gmail.com'; // Votre adresse Gmail
        $mail->Password = 'gzcy hplu shed ugoe';  // Remplacez par votre mot de passe d'application
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Configurer l'expéditeur et le destinataire de l'e-mail
        $mail->setFrom('getflix650@gmail.com', 'Getflix');
        $mail->addAddress($email);

        // Contenu de l'e-mail
        $mail->isHTML(true); // Utiliser le format HTML
        $mail->Subject = 'Votre code de sécurité';
        $mail->Body = "Votre code de sécurité est : $security_code";

        // Envoi de l'e-mail
        $mail->send();
        echo 'Le message a été envoyé';
    } catch (Exception $e) {
        // En cas d'erreur, afficher un message d'erreur
        echo "Le message n'a pas pu être envoyé. Erreur de Mailer : {$mail->ErrorInfo}";
    }
}

// Vérifier si la requête est une soumission de formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    try {
        // Requête SQL pour récupérer les informations de l'utilisateur
        $sql = "SELECT users.id, users.username, users.email, users.role, user_conf.password
                FROM users
                INNER JOIN user_conf ON users.id = user_conf.user_id
                WHERE users.username = :username OR users.email = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();

        // Récupérer les résultats de la requête
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Vérifier si l'utilisateur existe et si le mot de passe est correct
        if ($user && password_verify($password, $user['password'])) {
            // Générer un code de sécurité aléatoire
            $security_code = rand(0, 100);

            // Générer 4 autres options aléatoires différentes du code de sécurité
            $options = [$security_code];
            while (count($options) < 5) {
                $rand_option = rand(0, 100);
                if (!in_array($rand_option, $options)) {
                    $options[] = $rand_option;
                }
            }
            shuffle($options); // Mélanger les options pour les rendre aléatoires

            // Stocker les options dans la session pour les afficher sur la page de vérification 2FA
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['attempts'] = 0;
            $_SESSION['security_options'] = $options;
            $_SESSION['security_code'] = $security_code;

            // Mettre à jour le code de sécurité dans la base de données
            $query = "UPDATE user_conf SET security_code = :security_code WHERE user_id = :user_id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                'security_code' => $security_code,
                'user_id' => $user['id']
            ]);

            // Envoyer le code de sécurité à l'utilisateur par e-mail
            sendSecurityCode($user['email'], $security_code);

            // Rediriger l'utilisateur vers la page de vérification 2FA
            header("Location: ../public/verify_2fa.php");
            exit;
        } else {
            // Si les informations de connexion sont incorrectes, afficher un message d'erreur
            echo "Invalid login credentials.";
        }
    } catch (PDOException $e) {
        // En cas d'erreur SQL, enregistrer l'erreur dans un fichier log
        error_log($e->getMessage(), 3, "../logs/error.log");
        echo "Une erreur est survenue. Veuillez réessayer plus tard.";
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
        </div>
        <div class="input-box">
            <input type="password" placeholder="Password" name="password" required>
        </div>
        <div class="remember-forget">
            <a href="forgot_password.php">Forgot password?</a>
        </div>
        <button type="submit" class="btn">Login</button>
        <div class="register-link">
            <p>Don't have an account?<br> <a href="../public/register.php">Sign Up</a></p>
        </div>
    </form>
</div>
</body>
</html>
