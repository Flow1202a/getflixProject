<?php
global $pdo;
require_once '../includes/db_connect.php'; // Assurez-vous que le chemin est correct

$api_key = 'eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI5MzYwOTQzZDU1NGZlNTg3N2Y2YjcwZDZkMDNiYjZhYSIsIm5iZiI6MTcyMjYwNjc2OS45NTU2MDMsInN1YiI6IjY2YWNlM2U5OWQ1OTFhZDA0MGQyZTc5MiIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.kGdnLpXJprFaL4OKUApmNyqsV8LiF42IGeU-yPAjvqE';

// Requête pour obtenir les films
$query = "SELECT id FROM movies";
$stmt = $pdo->query($query);
$movies = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($movies as $movie) {
    $movie_id = $movie['id'];

    // Requête à l'API TMDB pour obtenir les informations du film
    $api_url = "https://api.themoviedb.org/3/movie/{$movie_id}?language=en-US";
    $headers = [
        "Authorization: Bearer {$api_key}",
        "Content-Type: application/json;charset=utf-8"
    ];

    $context = stream_context_create([
        "http" => [
            "method" => "GET",
            "header" => $headers
        ]
    ]);

    $response = file_get_contents($api_url, false, $context);
    $data = json_decode($response, true);

    if ($data && isset($data['vote_average'])) {
        $rating = $data['vote_average'];

        // Mise à jour de la note du film dans la base de données
        $update_query = "UPDATE movies SET rating = :rating WHERE id = :id";
        $update_stmt = $pdo->prepare($update_query);
        $update_stmt->execute(['rating' => $rating, 'id' => $movie_id]);

        echo "Updated rating for movie ID: {$movie_id}\n";
    } else {
        echo "Aucun rating trouvé pour l'ID du film : {$movie_id}\n";
    }
}
