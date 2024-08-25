<?php
session_start();
global $pdo;
require_once('../includes/db_connect.php');

// Vérifier si l'identifiant du film est présent dans l'URL
if (isset($_GET['id'])) {
    $idMovie = $_GET['id'];
} else {
    die("L'identifiant du film est requis.");
}

// Préparer et exécuter la requête SQL de manière sécurisée
$sql = "SELECT * FROM movies WHERE movies.id = :idMovie";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':idMovie', $idMovie, PDO::PARAM_STR);
$stmt->execute();
$movie = $stmt->fetch(PDO::FETCH_ASSOC);

// Vérifier si le film existe
if (!$movie) {
    die('Le film n\'existe pas.');
}

// Vérifier si l'utilisateur est connecté
$user_id = $_SESSION['user_id'] ?? null;

// Vérifier si le film est déjà dans les favoris
$is_favorite = false;
if ($user_id) {
    $query = "SELECT * FROM user_movies WHERE user_id = :user_id AND movie_id = :movie_id AND favorite = 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['user_id' => $user_id, 'movie_id' => $idMovie]);
    $is_favorite = $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
}

// URL de base pour les images et les bandes-annonces
$base_image_url = 'https://image.tmdb.org/t/p/w500';
$base_trailer = 'https://www.youtube.com/embed/';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($movie['title']); ?></title>
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css" />
    <link rel="stylesheet" href="../style/movieStyle.css">
    <link rel="stylesheet" href="../style/style.css">
</head>
<body>

<header>
    <nav class="nav position-absolute">
        <i class="uil uil-bars navOpenBtn"></i>
        <a href="#" class="logo">GetFlixDeNullos</a>

        <ul class="nav-links align-items-center">
            <i class="uil uil-times navCloseBtn"></i>
            <li><a href="#">Home</a></li>
            <li><a href="#">Categories</a></li>
            <li><a href="#">WatchList</a></li>
            <li><a href="#">Account</a></li>
            <li><a href="#">Connexion</a></li>
        </ul>

        <i class="uil uil-search search-icon" id="searchIcon"></i>
        <div class="search-box">
            <i class="uil uil-search search-icon"></i>
            <input type="text" placeholder="Search here..." />
        </div>
    </nav>
</header>

<div class="container">
    <h1 class="reveal"><?php echo htmlspecialchars($movie['title']); ?></h1>

    <div class="overview reveal">
        <?php echo htmlspecialchars($movie['overview']); ?>
    </div>

    <div class="movieImage">
        <img class="reveal" data-src="<?php echo htmlspecialchars($base_image_url . $movie['movies_image']); ?>" alt="photo du film">
    </div>

    <div>
        <iframe class="trailer reveal" width="500" height="315" src="<?php echo htmlspecialchars($base_trailer . $movie['movies_trailer']); ?>"></iframe>
    </div>

    <div class="rel_date">
        <p><strong>Date de sortie:</strong> <?php echo htmlspecialchars($movie['release_date']); ?></p>
    </div>

    <div class="movie-genres">
        <strong>Genres:</strong> <?php echo htmlspecialchars($movie['movies_genres']); ?>
    </div>

    <div class="rating reveal">
        <?php echo htmlspecialchars($movie['rating']) . '/10'; ?>
    </div>

    <!-- Bouton Étoile pour ajouter aux favoris -->
    <div class="favorite-section">
        <?php if ($user_id): ?>
            <form method="post" action="toggle_favorite.php">
                <input type="hidden" name="movie_id" value="<?php echo $idMovie; ?>">
                <button type="submit" name="toggle_favorite" class="favorite-btn">
                    <?php echo $is_favorite ? '★' : '☆'; ?>
                </button>
            </form>
        <?php else: ?>
            <p>Connectez-vous pour ajouter ce film à vos favoris.</p>
        <?php endif; ?>
    </div>

    <div class="actor_name reveal">
        <?php echo htmlspecialchars($movie['artist_name']); ?>
    </div>

    <div class="actor_image reveal">
        <?php
        try {
            $stmt = $pdo->prepare("SELECT artist_image_url FROM movies WHERE id = :id");
            $stmt->execute(['id' => $idMovie]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row && !empty($row['artist_image_url'])) {
                $artist_image_url = $row['artist_image_url'];
                $image_paths = explode(', ', $artist_image_url);
                foreach ($image_paths as $path) {
                    $full_image_url = $base_image_url . ltrim($path, '/');
                    echo '<img data-src="' . htmlspecialchars($full_image_url) . '" alt="Artist Image"><br>';
                }
            } else {
                echo "Aucune image trouvée.";
            }
        } catch (PDOException $e) {
            die("Échec de la requête : " . $e->getMessage());
        }
        ?>
    </div>
</div>

<section class="footer">
    <!-- Footer content -->
</section>

<button id="backToTop" title="Retour en haut">⬆️</button>

<script src="../javascript/scriptMovie.js"></script>

</body>
</html>
