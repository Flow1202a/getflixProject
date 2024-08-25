<?php
session_start();

// Liste des pages qui ne nécessitent pas d'être connecté
$excluded_pages = ['forgot_password.php', 'reset_password.php', 'security_questions.php'];

$current_page = basename($_SERVER['PHP_SELF']);

// Si la page actuelle n'est pas dans la liste des pages exclues et que l'utilisateur n'est pas connecté, rediriger vers logintest.php
if (!isset($_SESSION['user_id']) && !in_array($current_page, $excluded_pages)) {
    header('Location: ../public/logintest.php');
    exit;
}

// Pour les pages nécessitant des droits d'admin
function checkAdmin() {
    if ($_SESSION['role'] !== 'admin') {
        header('Location: ../public/index.php');
        exit;
    }
}
?>
