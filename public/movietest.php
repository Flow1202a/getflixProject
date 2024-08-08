<?php
require_once('../includes/db_connect.php');

$idMovie = $_GET['id'];

// URL de la base pour les images
$base_image_url = 'https://image.tmdb.org/t/p/w500';

// URL de la base pour les images
$base_trailer = 'https://www.youtube.com/embed/';

    // Prepare a secure SQL statement
    $sql = "SELECT * FROM movies WHERE movies.id = :idMovie";
    $stmt = $pdo->prepare($sql);

    // Bind parameters to prevent SQL injection
    $stmt->bindParam(':idMovie', $idMovie, PDO::PARAM_STR);

    // Execute the prepared statement
    $stmt->execute();
    $movie = $stmt->fetch(PDO::FETCH_ASSOC);
    // VERIFIE SI MOVIES EST PAS VIDE
    if(!empty($movie)){
        // SI MOVIE PAS VIDE ON FAIT CE QUE ON A FAIRE
  //      $movie = json_encode($movie, JSON_UNESCAPED_UNICODE);
        var_dump($movie);
    } else {
        // PAS DE MOVIE SOIT ON AFFICHE MESSAGE INEXISTANT SOIT REDIRECTION VERS INDEX
        echo 'existe pas';
    }
?>

<h1>
    <?php 
    echo $movie['title'];?>
</h1>

<div class="overview">
<?php 
echo $movie['overview'];
?>
</div>

<div class="movieImage">
    <img src="<?php echo htmlspecialchars($base_image_url . ($movie['movies_image']));?>"
</div>

<div>
    <iframe class="trailer" width="420" height="315" src="<?php echo ($base_trailer . ($movie['movies_trailer']));?>"></iframe>
    <style> 
        .trailer {
            position: none;
            
        }
    </style>
</div>

<div class="rating">
    <?php echo $movie['rating'] . '/10'; ?>
</div>

<div class="actor_name">
    <?php echo $movie['artist_name']; ?>
</div>

<div class="actor_image">
<?php
// Base URL pour les images
$base_image_url = "https://image.tmdb.org/t/p/w500/";

// Récupération de l'identifiant du film depuis l'URL ou autre source
if (isset($_GET['id'])) {
    $movie_id = (int)$_GET['id']; // Convertit l'id en entier pour plus de sécurité
} else {
    die("L'identifiant du film est requis.");
}

// Préparer et exécuter la requête pour récupérer la colonne 'artist_image_url'
try {
    $stmt = $pdo->prepare("SELECT artist_image_url FROM movies WHERE id = :id");
    $stmt->execute(['id' => $movie_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifiez si la colonne 'artist_image_url' est récupérée
    if ($row && isset($row['artist_image_url'])) {
        $artist_image_url = $row['artist_image_url'];

        // Séparer les chemins d'image en un tableau
        $image_paths = explode(', ', $artist_image_url);

        // Construit les URLs complètes pour chaque image
        $image_urls = array_map(function($path) use ($base_image_url) {
            return $base_image_url . ltrim($path, '/'); // ltrim() pour enlever le slash initial
        }, $image_paths);

        // Affiche les URLs des images
        foreach ($image_urls as $url) {
            echo "<img src=\"$url\" alt=\"Artist Image\"><br>";
        }
    } else {
        echo "Aucune image trouvée.";
    }
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}
?>
</div>