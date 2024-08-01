<?php
// Informations de connexion à la base de données
$servername = "db";
$username = "test";
$password = "pass";
$dbname = "demo";

try {
    // Connexion à la base de données MySQL avec PDO
    $dsn = "mysql:host=$servername;dbname=$dbname;charset=utf8";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connecté avec succès à la base de données MySQL<br>";

} catch (PDOException $e) {
    die("La connexion a échoué : " . $e->getMessage());
}

// Token d'authentification pour TheTVDB
$tvdbToken = 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJhZ2UiOiIiLCJhcGlrZXkiOiJhYjNmYWY2MS02YjI4LTRmNGItOGM1Mi0wOWE4ZWVjYzhkMDciLCJjb21tdW5pdHlfc3VwcG9ydGVkIjpmYWxzZSwiZXhwIjoxNzI1MDU5NTkzLCJnZW5kZXIiOiIiLCJoaXRzX3Blcl9kYXkiOjEwMDAwMDAwMCwiaGl0c19wZXJfbW9udGgiOjEwMDAwMDAwMCwiaWQiOiIyNDcxNzkwIiwiaXNfbW9kIjpmYWxzZSwiaXNfc3lzdGVtX2tleSI6ZmFsc2UsImlzX3RydXN0ZWQiOmZhbHNlLCJwaW4iOm51bGwsInJvbGVzIjpbXSwidGVuYW50IjoidHZkYiIsInV1aWQiOiIifQ.JiX9WS7RzmlRbiu9QVLsD1EAymeMs3pEKV5bt8fNjFhQsO3SlM-P1SsenKG3NS2DMBx5AnaODexmcnX7rkKfverM3QrF6QJSBvVkk1gHccb25fMgSWrEZ3JiZqQSD_oy_4qgnZkk0nlpSRy40QzG1Ik78xe3v-Sqjv-RbVGNJr_es2d7vUDzQ6FVzoFM-qqTgpqwn4tsg1hyJiev6EZpk5OYHY-lhrvjReuEuYANtjxJL3SEMpnIgywHSUHaBsJMzkBw7BGQUZs330nENHJZhf9G61hKlKuguDaA2Dmf0gyPjSR1WFW3oqJhEGW4Ku1frdnjN6W5cxwWId3u1W8o2ldO_Pd6tw9UWapuUpMxQ8brqAVFSoGwLv10AmzTKc2C3M3cHmJ2-Q_DRqRVu_vu73O6AtdkDhqzjy_8PjV9ppBuQXwpruO9Or5opOjR3uXOSQKVonH4WOikrUY1NMDAcuevuT4WgHtu6umJ3du_D8pdTMQML4SagaEx_5IJ42Z3qq1dNPvmNRd2ZsEB_Mfnqj9P-RxHc7oi0ORkasdSLjk1S-PYg5X_USJ9r8eHFeKhMqBeas4-Ah6Br7f-hSCNvK46Ldre31NVuWrjbmmFDdYehryxDs-tMy8ATH9DamWDshdBCxuqT07SCMGSDjoJqbh42jeYunyjviq-ha7EvWo';  // Remplacez cette valeur par le token obtenu précédemment

// Fonction pour obtenir les données depuis l'API avec gestion des erreurs
function getApiData($url, $headers = []) {
    $options = array(
        'http' => array(
            'header'  => implode("\r\n", $headers) . "\r\n",
            'method'  => 'GET',
        ),
    );

    $context  = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);

    if ($result === FALSE) {
        $error = error_get_last();
        die("Erreur de connexion à l'API : " . $error['message']);
    }

    $data = json_decode($result, true);

    if (isset($data['Error'])) {
        die("Erreur de l'API : " . $data['Error']);
    }

    return $data;
}

// Récupérer les séries populaires depuis l'API TheTVDB
$seriesUrl = 'https://api.thetvdb.com/series';
$series = [];
$page = 1;
while (count($series) < 100) { // Limite à 100 séries pour les besoins de l'exemple
    $url = $seriesUrl . "?page=$page";
    $data = getApiData($url, ["Authorization: Bearer $tvdbToken"]);
    if (!isset($data['data']) || empty($data['data'])) {
        break;
    }
    $series = array_merge($series, $data['data']);
    $page++;
}

// Récupérer les films populaires (si disponibles) depuis l'API TheTVDB
$moviesUrl = 'https://api.thetvdb.com/movies';  // Exemple d'URL pour les films (à vérifier avec la documentation de l'API)
$movies = [];
$page = 1;
while (count($movies) < 100) { // Limite à 100 films pour les besoins de l'exemple
    $url = $moviesUrl . "?page=$page";
    $data = getApiData($url, ["Authorization: Bearer $tvdbToken"]);
    if (!isset($data['data']) || empty($data['data'])) {
        break;
    }
    $movies = array_merge($movies, $data['data']);
    $page++;
}

// Fusionner les séries et les films dans une seule variable
$films = array_merge($series, $movies);

// Insérer les films (séries et films) dans la base de données
foreach ($films as $item) {
    $filmId = $item['id'];
    $titre = $conn->real_escape_string($item['seriesName']);  // Peut nécessiter un autre champ pour les films
    $photo = $conn->real_escape_string('https://thetvdb.com/banners/' . $item['banner']);
    $synopsis = $conn->real_escape_string($item['overview']);
    $note = $item['siteRating'];
    $dateSortie = !empty($item['firstAired']) ? $conn->real_escape_string($item['firstAired']) : NULL;

    // Déterminer le type (série ou film)
    $type = isset($item['type']) ? $item['type'] : 'serie';  // Par défaut à 'serie'

    // Insérer les données dans la table films
    $sql = "INSERT INTO films (id, titre, photo, synopsis, trailer, note, date_sortie, type)
    VALUES ('$filmId', '$titre', '$photo', '$synopsis', '', '$note', '$dateSortie', '$type')";

    if ($conn->query($sql) === TRUE) {
        echo "Le '$type' '$titre' a été inséré avec succès<br>";
    } else {
        echo "Erreur: " . $sql . "<br>" . $conn->error . "<br>";
    }

    // Récupérer les 5 premiers acteurs principaux pour chaque série/film
    $actorsUrl = "https://api.thetvdb.com/series/$filmId/actors";  // Exemple d'URL pour les acteurs
    $actorsData = getApiData($actorsUrl, ["Authorization: Bearer $tvdbToken"]);
    $acteurs = array_slice($actorsData['data'], 0, 5);

    foreach ($acteurs as $actor) {
        $nom = $conn->real_escape_string($actor['name']);
        $photo = $conn->real_escape_string('https://thetvdb.com/banners/' . $actor['image']);

        // Vérifier si l'acteur existe déjà
        $actorSql = "SELECT id FROM acteurs WHERE nom = '$nom'";
        $result = $conn->query($actorSql);
        if ($result->num_rows > 0) {
            // Acteur existe déjà, récupérer l'ID
            $actorRow = $result->fetch_assoc();
            $actorId = $actorRow['id'];
        } else {
            // Insérer le nouvel acteur dans la table acteurs
            $actorSql = "INSERT INTO acteurs (nom, photo) VALUES ('$nom', '$photo')";
            if ($conn->query($actorSql) === TRUE) {
                $actorId = $conn->insert_id;
            } else {
                echo "Erreur: " . $actorSql . "<br>" . $conn->error . "<br>";
                continue;
            }
        }

        // Insérer la relation série/film-acteur dans la table film_acteurs
        $filmActorSql = "INSERT INTO film_acteurs (film_id, acteur_id) VALUES ('$filmId', '$actorId')";
        if ($conn->query($filmActorSql) === TRUE) {
            echo "L'acteur '$nom' a été associé au '$type' '$titre'<br>";
        } else {
            echo "Erreur: " . $filmActorSql . "<br>" . $conn->error . "<br>";
        }
    }
}

// Fermer la connexion
$conn->close();

