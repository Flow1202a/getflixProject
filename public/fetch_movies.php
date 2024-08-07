<?php
global $pdo;
require_once '../includes/db_connect.php';

header('Content-Type: application/json');

try {
    $sql = "SELECT id, title, overview, movies_image FROM movies";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Vérifiez si la requête a retourné des résultats
    if ($movies === false) {
        throw new Exception('Erreur lors de l\'exécution de la requête.');
    }
    
    // Encodez en JSON
    $json = json_encode($movies, JSON_UNESCAPED_UNICODE);
    
    // Vérifiez les erreurs d'encodage JSON
    if ($json === false) {
        throw new Exception('Erreur lors de l\'encodage en JSON : ' . json_last_error_msg());
    }
    
    echo $json;
} catch (Exception $e) {
    // Afficher les erreurs avec un code de statut approprié
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
