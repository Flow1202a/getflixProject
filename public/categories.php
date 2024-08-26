<?php
global $pdo;
session_start();
require_once('../includes/db_connect.php');
$base_image_url = 'https://image.tmdb.org/t/p/w500';

$allowed_genres = [
    'drama',
    'action',
    'adventure',
    'thriller',
    'comedy',
    'romance',
    'science fiction',
    'animation',
    'family',
    'mystery',
    'fantasy',
    'horror',
    'western',
    'crime'
];

// Récupérer tous les genres distincts
$query = "SELECT DISTINCT movies_genres FROM movies";
$stmt = $pdo->query($query);
$allGenres = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Créer un tableau pour stocker les genres autorisés seulement
$genres = [];
foreach ($allGenres as $row) {
    $genresArray = explode(',', $row['movies_genres']);
    foreach ($genresArray as $genre) {
        $genre = trim($genre); // Supprimer les espaces éventuels
        if (in_array(strtolower($genre), $allowed_genres) && !in_array($genre, $genres)) {
            $genres[] = $genre;
        }
    }
}

// Trier les genres par ordre alphabétique
sort($genres);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Catégories de Films</title>
    <link rel="stylesheet" href="../style/categorieStyle.css">
    <script src="../javascript/script.js" defer></script>
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

</head>
<body>

<header>
    <nav class="nav position-fixed w-100">
        <i class="uil uil-bars navOpenBtn"></i>
        <a href="index.php" class="logo">GetFlixDeNullos</a>

        <ul class="nav-links align-items-center">
            <i class="uil uil-times navCloseBtn"></i>
            <li><a href="index.php">Home</a></li>
            <li><a href="categories.php">Categories</a></li>
            <li><a href="../public/account.php">Account</a></li>
            <?php if (isset($_SESSION['role'])): ?>
                <li><a href="../includes/logout.php">Se déconnecter</a></li>
            <?php else: ?>
                <li><a href="logintest.php">Connexion</a></li>
            <?php endif; ?>
        </ul>

        <i class="uil uil-search search-icon" id="searchIcon"></i>
        <div class="search-box">
            <i class="uil uil-search search-icon"></i>
            <!-- Formulaire de recherche -->
            <form action="search_results.php" method="get">
                <input class="searchTarget" type="text" name="q" placeholder="Rechercher un film, un artiste, ou un genre..." />
                <!-- Le bouton est nécessaire pour les soumissions par "Entrer", mais reste invisible -->
                <button type="submit" style="display: none;"></button>
            </form>
        </div>
    </nav>
</header>

<?php foreach ($genres as $genre): ?>
    <div class="category" id="<?php echo htmlspecialchars($genre); ?>">
        <h2><?php echo htmlspecialchars($genre); ?></h2>
        <button class="nav-arrow left" onclick="scrollCategoryLeft('<?php echo htmlspecialchars($genre); ?>')">&#9664;</button>
        <div class="movies-container" id="container-<?php echo htmlspecialchars($genre); ?>">
            <?php
            // Récupérer les films pour chaque genre avec une correspondance exacte
            $query = "SELECT * FROM movies WHERE FIND_IN_SET(:genre, movies_genres) > 0";
            $stmt = $pdo->prepare($query);
            $stmt->execute([':genre' => $genre]);
            $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <?php foreach ($movies as $movie): ?>
                <div class="movie align-items-center">
                    <img src="<?php echo htmlspecialchars($base_image_url . htmlspecialchars($movie['movies_image'])); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                    <a href="movie.php?id=<?php echo htmlspecialchars($movie['id']); ?>"><?php echo htmlspecialchars($movie['title']); ?></a>
                </div>
            <?php endforeach; ?>
        </div>
        <button class="nav-arrow right" onclick="scrollCategoryRight('<?php echo htmlspecialchars($genre); ?>')">&#9654;</button>
    </div>
<?php endforeach; ?>

<script>
    function scrollCategoryLeft(categoryId) {
        var container = document.getElementById('container-' + categoryId);
        container.scrollBy({ left: -1500, behavior: 'smooth' });
    }

    function scrollCategoryRight(categoryId) {
        var container = document.getElementById('container-' + categoryId);
        container.scrollBy({ left: 1500, behavior: 'smooth' });
    }
</script>
</body>
</html>
