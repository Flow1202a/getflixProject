<?php
global $pdo;
require_once '../includes/db_connect.php';
require_once '../includes/api_fetch.php';

// Récupérer les données de l'API
$data = fetchMoviesFromApi();

// Vérifier que les données sont bien récupérées et qu'il s'agit d'un tableau
if ($data && is_array($data) && isset($data['results'])) {
    // Préparer la requête d'insertion
    $sql = "INSERT INTO movies (id, title, overview, movies_image) VALUES (:id, :title, :overview, :poster_path)";
    $stmt = $pdo->prepare($sql);

    // Extraire les données nécessaires de la réponse de l'API
    foreach ($data['results'] as $movie) { // Assurez-vous que 'results' est la clé correcte
        $id = $movie['id'];
        $title = $movie['title'];
        $description = $movie['overview'];
        $movies_image = $movie['poster_path'];

        // Lier les paramètres et exécuter la requête
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':overview', $description);
        $stmt->bindParam(':id', $id);   
        $stmt->bindParam(':poster_path', $movies_image);
        $stmt->execute();
    }

    echo "Les données ont été insérées avec succès.";
} else {
    echo "Erreur lors de la récupération des données de l'API.";
}