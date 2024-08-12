<?php
require_once('../includes/db_connect.php');

// Fonction pour échapper les données de sortie
function escapeHtml($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Récupération de l'identifiant du film depuis l'URL
$idMovie = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$idMovie) {
    die("L'identifiant du film est requis.");
}

// URL de base pour les images et les bandes-annonces
$base_image_url = 'https://image.tmdb.org/t/p/w500/';
$base_trailer = 'https://www.youtube.com/embed/';

// Préparation et exécution de la requête SQL
$sql = "SELECT * FROM movies WHERE id = :idMovie";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':idMovie', $idMovie, PDO::PARAM_INT);
$stmt->execute();
$movie = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$movie) {
    die('Le film n\'existe pas.');
}

// Extraction des données du film et échappement
$title = escapeHtml($movie['title']);
$overview = escapeHtml($movie['overview']);
$movie_image = escapeHtml($base_image_url . $movie['movies_image']);
$trailer_url = escapeHtml($base_trailer . $movie['movies_trailer']);
$rating = escapeHtml($movie['rating']);
$artist_name = escapeHtml($movie['artist_name']);
$artist_image_url = $movie['artist_image_url'];
$release_date = escapeHtml($movie['release_date']); // Ajout de la date de sortie

?>

<h1><?php echo $title; ?></h1>

<div class="overview">
    <?php echo $overview; ?>
</div>

<div class="movieImage">
    <img src="<?php echo $movie_image; ?>" alt="Image du film">
</div>

<div>
    <iframe class="trailer" width="420" height="315" src="<?php echo $trailer_url; ?>" frameborder="0" allowfullscreen></iframe>
    <style>
        .trailer {
            position: relative;
        }
    </style>
</div>

<div class="rating">
    <?php echo $rating . '/10'; ?>
</div>

<div class="actor_name">
    <?php echo $artist_name; ?>
</div>

<div class="actor_image">
    <?php
    if ($artist_image_url) {
        // Séparer les chemins d'image en un tableau
        $image_paths = explode(', ', $artist_image_url);

        // Construit les URLs complètes pour chaque image
        $image_urls = array_map(function($path) use ($base_image_url) {
            return $base_image_url . ltrim($path, '/'); // ltrim() pour enlever le slash initial
        }, $image_paths);

        // Affiche les URLs des images
        foreach ($image_urls as $url) {
            echo "<img src=\"$url\" alt=\"Image de l'artiste\"><br>";
        }
    } else {
        echo "Aucune image trouvée.";
    }
    ?>
</div>

<p>Date de sortie : <?php echo $release_date; ?></p>
