<?php
global $pdo;
session_start();
require_once '../includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../public/forgot_password.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $answer = trim($_POST['answer']);

    $query = "SELECT security_answer, security_code FROM user_conf WHERE user_id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (password_verify($answer, $result['security_answer']) &&
        $_POST['security_code'] == $result['security_code']) {
        // Rediriger vers la page de réinitialisation du mot de passe
        header('Location: ../public/reset_password.php');
        exit;
    } else {
        echo "Réponse ou code incorrect.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Question de sécurité</title>
    <link rel="stylesheet" href="../style/registerLoginStyle.css">
</head>
<body>
<div class="wrapper">
    <header>Question de sécurité</header>
    <form method="post" action="">
        <p><?php echo $_SESSION['security_question']; ?></p>
        <div class="input-box">
            <label for="answer"></label><input type="text" id="answer" name="answer" placeholder="Votre réponse" required>
        </div>
        <div class="input-box">
            <label for="security_code"></label><input type="number" id="security_code" name="security_code" placeholder="Votre code de sécurité" required>
        </div>
        <button type="submit" class="btn">Valider</button>
    </form>
</div>
</body>
</html>
