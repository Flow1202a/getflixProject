<?php
session_start();
global $pdo;
require_once('../includes/db_connect.php');

// Vérifier si l'utilisateur est connecté et a accès à la page de vérification
if (!isset($_SESSION['user_id'])) {
    header('Location: logintest.php');
    exit;
}

// Initialiser la variable de tentatives si ce n'est pas déjà fait
if (!isset($_SESSION['attempts'])) {
    $_SESSION['attempts'] = 0;
}

// Vérification du code de sécurité soumis par l'utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_code = $_POST['security_code'];

    try {
        // Récupérer le code de sécurité stocké dans la session
        if ($selected_code == $_SESSION['security_code']) {
            // Code correct, authentification réussie
            $_SESSION['authenticated'] = true;
            $_SESSION['attempts'] = 0; // Réinitialiser les tentatives
            header('Location: index.php');
            exit;
        } else {
            // Code incorrect, incrémenter les tentatives
            $_SESSION['attempts']++;

            // Vérifier si le nombre de tentatives dépasse la limite
            if ($_SESSION['attempts'] > 1) {
                // Bloquer le compte et rediriger
                header('Location: account_locked.php'); // Rediriger vers une page qui informe que le compte est bloqué
                exit;
            } else {
                $error_message = "Le code de sécurité est incorrect. Il vous reste une tentative.";
            }
        }
    } catch (PDOException $e) {
        error_log($e->getMessage(), 3, "../logs/error.log");
        $error_message = "Une erreur est survenue. Veuillez réessayer plus tard.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification 2FA</title>
    <link rel="stylesheet" href="../style/registerLoginStyle.css">
</head>
<body>
<div class="wrapper">
    <header>Vérification 2FA</header>
    <form method="post">
        <p>Choisissez le code de sécurité que vous avez reçu par email :</p>
        <div class="input-box">
            <?php
            // Afficher les cinq options de code de sécurité sous forme de boutons radio
            foreach ($_SESSION['security_options'] as $option) {
                echo '<div class="radio-option">';
                echo '<input type="radio" id="option_' . $option . '" name="security_code" value="' . $option . '" required>';
                echo '<label for="option_' . $option . '">' . $option . '</label>';
                echo '</div>';
            }
            ?>
        </div>
        <?php
        // Afficher un message d'erreur si nécessaire
        if (isset($error_message)) {
            echo '<div class="error-message">' . $error_message . '</div>';
        }
        ?>
        <button type="submit" class="btn">Vérifier</button>
    </form>
</div>
</body>
</html>
