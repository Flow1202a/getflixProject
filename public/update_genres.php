<?php
require_once '../includes/db_connect.php'; // Connexion à la base de données

// Requête pour récupérer tous les films de la base de données
$query = "SELECT id FROM movies";
$stmt = $pdo->query($query);
$movies = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Parcourir chaque film pour récupérer les genres via l'API
foreach ($movies as $movie) {
    $movie_id = $movie['id'];
    
    // Appel à l'API pour récupérer les détails du film, y compris les genres
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://api.themoviedb.org/3/movie/$movie_id?language=en-US",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiIzODVlMjM2MjVlN2IxNzA4Y2U2YzQxZjY5YWEwMTQyOSIsIm5iZiI6MTcyMzAzODYwNi40MzA5MjksInN1YiI6IjY2YjM3YWY0MzIzZDY3ZjhkMDkyZTNhMiIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.gDi0Los2cvzfAdxLkIA_vPly75nnvchTZKnPDlM7H68", // Remplacez par votre clé API
            "accept: application/json"
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "Erreur cURL : " . $err;
    } else {
        $data = json_decode($response, true);
        
        if (isset($data['genres'])) {
            // Récupérer les noms des genres
            $genres = array_map(function($genre) {
                return $genre['name'];
            }, $data['genres']);

            // Convertir le tableau des genres en une chaîne séparée par des virgules
            $genres_string = implode(', ', $genres);

            // Mettre à jour la colonne 'movies_genres' dans la base de données
            $update_query = "UPDATE movies SET movies_genres = :genres WHERE id = :id";
            $update_stmt = $pdo->prepare($update_query);
            $update_stmt->execute([
                ':genres' => $genres_string,
                ':id' => $movie_id,
            ]);

            echo "Genres pour le film ID $movie_id mis à jour avec succès : $genres_string\n";
        } else {
            echo "Aucun genre trouvé pour le film ID : $movie_id\n";
        }
    }
}
?>
