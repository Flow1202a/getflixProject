<!-- Ici, on va rajouter un script qui va permettre de rajouter dans la base de données le nom et photos des artistes -->
<?php
global $pdo;
require_once '../includes/db_connect.php'; // Assurez-vous que le chemin est correct

$api_key = 'eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiIzODVlMjM2MjVlN2IxNzA4Y2U2YzQxZjY5YWEwMTQyOSIsIm5iZiI6MTcyMzAzODYwNi40MzA5MjksInN1YiI6IjY2YjM3YWY0MzIzZDY3ZjhkMDkyZTNhMiIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.gDi0Los2cvzfAdxLkIA_vPly75nnvchTZKnPDlM7H68'; // Remplacez par votre clé API TMDB

// Requête pour obtenir les films
$query = "SELECT id FROM movies";
$stmt = $pdo->query($query);
$movies = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($movies as $movie) {
    $movie_id = $movie['id'];

    // Requête à l'API TMDB pour obtenir les informations du film
    $api_url = "https://api.themoviedb.org/3/movie/{$movie_id}/credits";
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
    $credits = json_decode($response, true);

    if (isset($credits['cast'])) {
        // Accède au tableau 'cast'
        $actors = array_slice($credits['cast'], 0, 3);
    
        // Initialise les tableaux pour les noms et les chemins d'images
        $artist_names = [];
        $artist_images = [];
    
        // Parcourt les trois premiers éléments du tableau 'cast'
        foreach ($actors as $actor) {
            // Vérifie si la clé 'name' existe dans le sous-tableau
            if (isset($actor['name'])) {
                $artist_names[] = $actor['name'];
            }
            // Vérifie si la clé 'profile_path' existe dans le sous-tableau
            if (isset($actor['profile_path'])) {
                $artist_images[] = $actor['profile_path'];
            }
        }

        $stmt_update = $pdo->prepare("
            UPDATE movies 
            SET artist_name = :artist_name, 
                artist_image_url = :artist_image_url 
            WHERE id = :movie_id
        ");

        $stmt_update->execute([
            ':artist_name' => implode(', ', $artist_names), 
            ':artist_image_url' => implode(', ', $artist_images), 
            ':movie_id' => $movie_id,
        ]);

        echo "Film:, Acteurs: " . implode(', ', $artist_names) . "<br>";
    } else {
        echo "Aucun acteur trouvé pour le film:<br>";
    }
}; 