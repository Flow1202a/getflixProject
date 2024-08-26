<?php
// Connexion à la base de données MySQL
$host = 'localhost';  // Hostinger utilise généralement 'localhost' pour MySQL
$db = 'u603046621_getflix_db';  // Nom de la base de données
$user = 'u603046621_root';  // Nom d'utilisateur MySQL que tu as défini
$pass = 'getflixvapayeR.25';  // Mot de passe MySQL que tu as défini

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Erreur de connexion : ' . $e->getMessage());
}
