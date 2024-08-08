<?php
global $pdo;
require_once '../includes/db_connect.php'; // Assurez-vous que le chemin est correct

// Remplacez cette clé par votre propre clé API valide
$api_key = 'eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI5MzYwOTQzZDU1NGZlNTg3N2Y2YjcwZDZkMDNiYjZhYSIsIm5iZiI6MTcyMjYwNjc2OS45NTU2MDMsInN1YiI6IjY2YWNlM2U5OWQ1OTFhZDA0MGQyZTc5MiIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.kGdnLpXJprFaL4OKUApmNyqsV8LiF42IGeU-yPAjvqE';

try {
    // Sélectionner tous les films
    $query = "SELECT id FROM movies";
    $stmt = $pdo->query($query);
    $movie_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($movie_ids as $movie_id) {
        $url = "https://api.themoviedb.org/3/movie/$movie_id/videos?language=en-US";
        
        // Initialiser cURL
        $ch = curl_init();
        
        // Configurer les options cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer $api_key"
        ));
        
        // Exécuter la requête
        $response = curl_exec($ch);
        
        // Vérifier les erreurs
        if(curl_errno($ch)) {
            echo 'Erreur cURL: ' . curl_error($ch);
            curl_close($ch);
            continue;
        }
        
        curl_close($ch);
        
        // Décoder la réponse JSON
        $data = json_decode($response, true);
        
        if (isset($data['results']) && is_array($data['results'])) {
            $trailer_key = null;
            foreach ($data['results'] as $video) {
                if ($video['type'] === 'Trailer') {
                    $trailer_key = $video['key'];
                    break;
                }
            }

            if ($trailer_key) {
                $update_query = "UPDATE movies SET movies_trailer = :trailer_key WHERE id = :movie_id";
                $update_stmt = $pdo->prepare($update_query);
                $update_stmt->execute(['trailer_key' => $trailer_key, 'movie_id' => $movie_id]);
                echo "Trailer trouvé pour l'ID du film : $movie_id, clé : $trailer_key\n";
            } else {
                echo "Aucun trailer trouvé pour l'ID du film : $movie_id\n";
            }
        } else {
            echo "Erreur lors de la récupération des données pour l'ID du film : $movie_id\n";
        }
    }
} catch (PDOException $e) {
    echo 'Erreur de connexion : ' . $e->getMessage();
}
