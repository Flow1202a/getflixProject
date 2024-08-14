<?php
require_once '../includes/db_connect.php'; // Assurez-vous que ce fichier configure la connexion PDO dans la variable $pdo

$api_key = 'eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiIzODVlMjM2MjVlN2IxNzA4Y2U2YzQxZjY5YWEwMTQyOSIsIm5iZiI6MTcyMzAzODYwNi40MzA5MjksInN1YiI6IjY2YjM3YWY0MzIzZDY3ZjhkMDkyZTNhMiIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.gDi0Los2cvzfAdxLkIA_vPly75nnvchTZKnPDlM7H68'; // Remplacez par votre vrai jeton Bearer

// Récupérer les films depuis la base de données
$stmt = $pdo->query("SELECT id FROM movies");
$movies = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($movies as $movie) {
    $movie_id = $movie['id'];
    $url = "https://api.themoviedb.org/3/movie/$movie_id?language=en-US";

    // Initialiser cURL
    $curl = curl_init();

    // Configurer les options cURL
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer $api_key",
            "accept: application/json"
        ],
    ]);

    // Exécuter la requête
    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "Erreur cURL pour le film ID $movie_id : " . $err . "<br>";
        continue;
    }

    // Décoder la réponse JSON
    $movie_data = json_decode($response, true);

    // Debug: Afficher la réponse complète pour le film en question
    echo "<pre>";
    print_r($movie_data);
    echo "</pre>";

    // Vérification si release_date est disponible
    if (isset($movie_data['release_date']) && !empty($movie_data['release_date'])) {
        $release_date = $movie_data['release_date'];

        // Mettre à jour la base de données avec la release_date
        $stmt_update = $pdo->prepare("
            UPDATE movies 
            SET release_date = :release_date 
            WHERE id = :movie_id
        ");

        $stmt_update->execute([
            ':release_date' => $release_date,
            ':movie_id' => $movie_id,
        ]);

        echo "Film ID: $movie_id, Date de sortie: $release_date<br>";
    } else {
        echo "Aucune date de sortie trouvée pour le film ID: $movie_id<br>";
    }
}
?>
