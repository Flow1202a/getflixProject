<?php
global $pdo;
session_start(); // Assurez-vous que la session est démarrée

require '../includes/db_connect.php'; // Inclusion du fichier de connexion PDO

// Vérifiez si le script est exécuté via une requête HTTP POST
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer l'ID utilisateur depuis la session
    $user_id = $_SESSION['user_id'] ?? null;

    // Récupérer le nouveau mot de passe du formulaire
    $new_password = $_POST['new_password'] ?? null;

    if ($user_id && $new_password) {
        // Hachage du nouveau mot de passe
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

        // Mise à jour du mot de passe dans la base de données
        $stmt = $pdo->prepare("UPDATE user_conf SET password = :password WHERE user_id = :user_id");
        $stmt->execute(['password' => $hashed_password, 'user_id' => $user_id]);

        echo "Votre mot de passe a été réinitialisé avec succès.";
    } else {
        echo "Erreur : ID utilisateur ou nouveau mot de passe manquant.";
    }
} else {
    echo "Cette page doit être accédée via une requête POST.";
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
