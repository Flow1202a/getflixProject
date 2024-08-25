<?php
session_start();

// Détruire toutes les variables de session
$_SESSION = [];

// Détruire la session
session_destroy();

// Rediriger l'utilisateur vers la page d'accueil
header('Location: ../public/index.php');
exit;
