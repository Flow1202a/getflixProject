<?php
    require 'vendor/autoload.php';

    use GuzzleHttp\Client;


    // Configuration de l'API TMDB
    $apiKey = '35deb6f78ce3b66bbf5aa3b3a40ef939';
    $baseUrl = 'https://api.themoviedb.org/3';

    // Configuration de la base de données
    $servername = "db"; // Nom du conteneur MySQL dans Docker
    $username = "user";
    $password = "password";
    $dbname = "tmdb";

    try {
        $dsn = "mysql:host=$servername;dbname=$dbname;charset=utf8mb4";
        $conn = new PDO($dsn, $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        echo "Connexion à la base de données réussie.\n";

        // Initialiser le client HTTP
        $client = new Client();

        // Récupérer les 150 films les mieux notés
        $page = 1;
        $totalResults = 0;
        $movies = [];

        while ($totalResults < 150 && $page <= 7) { // Limite de 7 pages car chaque page contient 20 résultats
            $response = $client->request('GET', "$baseUrl/movie/top_rated", [
                'query' => [
                    'api_key' => $apiKey,
                    'language' => 'en-US',
                    'page' => $page
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            $movies = array_merge($movies, $data['results']);
            $totalResults += count($data['results']);
            $page++;
        }

        // Insérer les films dans la base de données
        $stmt = $conn->prepare("INSERT INTO movies (title, description, release_date, artist_name, artist_image_url, trailer_url, rating, actor_1, actor_2, actor_3, actor_4, actor_5) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        foreach (array_slice($movies, 0, 150) as $movie) {
            $title = $movie['title'];
            $description = $movie['overview'];
            $releaseDate = $movie['release_date'];
            $rating = $movie['vote_average'];

            // Récupérer les détails du film pour obtenir les acteurs principaux
            $movieId = $movie['id'];
            $response = $client->request('GET', "$baseUrl/movie/$movieId/credits", [
                'query' => [
                    'api_key' => $apiKey
                ]
            ]);

            $credits = json_decode($response->getBody(), true);
            $actors = array_column(array_slice($credits['cast'], 0, 5), 'name');

            // Remplir les noms des acteurs principaux (ou NULL si pas disponible)
            $actor1 = $actors[0] ?? null;
            $actor2 = $actors[1] ?? null;
            $actor3 = $actors[2] ?? null;
            $actor4 = $actors[3] ?? null;
            $actor5 = $actors[4] ?? null;

            // Vous devrez peut-être faire des requêtes supplémentaires pour obtenir artist_name, artist_image_url, et trailer_url
            $artistName = null; // à obtenir via une autre requête API ou définir une valeur par défaut
            $artistImageUrl = null; // à obtenir via une autre requête API ou définir une valeur par défaut
            $trailerUrl = null; // à obtenir via une autre requête API ou définir une valeur par défaut

            $stmt->execute([$title, $description, $releaseDate, $artistName, $artistImageUrl, $trailerUrl, $rating, $actor1, $actor2, $actor3, $actor4, $actor5]);
        }

        echo "Importation réussie des films.\n";
    } catch(PDOException $e) {
        echo "Erreur de connexion à la base de données : " . $e->getMessage() . "\n";
    } catch(Exception $e) {
        echo "Erreur : " . $e->getMessage() . "\n";
    }

    $conn = null;

