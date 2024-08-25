<?php
session_start();

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
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
