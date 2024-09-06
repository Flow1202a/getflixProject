<?php
global $pdo;
session_start();
require_once('../includes/db_connect.php');
$base_image_url = 'https://image.tmdb.org/t/p/w500';

// Récupérer le terme de recherche
$search_term = isset($_GET['q']) ? $_GET['q'] : '';

if ($search_term) {
    // Rechercher les films correspondant au terme de recherche dans les titres, les genres et les noms d'artistes
    $query = "SELECT * FROM movies WHERE title LIKE :search_term OR movies_genres LIKE :search_term OR artist_name LIKE :search_term";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':search_term' => '%' . $search_term . '%']);
    $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $movies = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Résultats de Recherche</title>
    <link rel="stylesheet" href="../style/searchStyle.css">
    <script src="../javascript/script.js" defer></script>
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
<<<<<<< HEAD

<header>
    <nav class="nav position-fixed w-100">
        <i class="uil uil-bars navOpenBtn"></i>
        <a href="index.php" class="logo">GetFlixDeNullos</a>
=======
    <header>
    <nav class="nav position-fixed w-100">
        <i class="uil uil-bars navOpenBtn"></i>
            <a href="index.php" class="logo">GetFlixDeNullos</a>
>>>>>>> 93c2816 (modif)

        <ul class="nav-links align-items-center">
            <i class="uil uil-times navCloseBtn"></i>
            <li><a href="index.php">Home</a></li>
            <li><a href="categories.php">Categories</a></li>
<<<<<<< HEAD
            <li><a href="../public/account.php">Account</a></li>
            <?php if (isset($_SESSION['role'])): ?>
                <li><a href="../includes/logout.php">Se déconnecter</a></li>
            <?php else: ?>
                <li><a href="logintest.php">Connexion</a></li>
            <?php endif; ?>
=======
            <li><a href="#">WatchList</a></li>
            <li><a href="../includes/back_office.php">Account</a></li>
            <li><a href="logintest.php">Connexion</a></li>
>>>>>>> 93c2816 (modif)
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
<<<<<<< HEAD
</header>

<div class="search-results-container" style="padding-top: 80px;">
    <?php if (empty($movies)): ?>
        <p>Aucun résultat trouvé pour "<?php echo htmlspecialchars($search_term); ?>"</p>
    <?php else: ?>
        <?php foreach ($movies as $movie): ?>
            <div class="search-movie">
                <img src="<?php echo htmlspecialchars($base_image_url . htmlspecialchars($movie['movies_image'])); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                <a href="movie.php?id=<?php echo htmlspecialchars($movie['id']); ?>"><?php echo htmlspecialchars($movie['title']); ?></a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<script src="../javascript/script.js" defer></script>
=======

    </header>
    <div class="results-container">
        <?php if (empty($movies)): ?>
            <p>Aucun résultat trouvé.</p>
        <?php else: ?>
            <?php foreach ($movies as $movie): ?>
                <div class="card moviecarte col-xs-12 col-sm-6 col-md-3 col-lg-2 mb-5 me-1 ">
                    <img src="<?php echo htmlspecialchars($base_image_url . htmlspecialchars($movie['movies_image'])); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                    <div class="card-body align-content-center">
                        <h5 class="card-title" style="color: #EED6D3"><?php echo htmlspecialchars($movie['title']); ?></h5>
                        <a  href="movie.php?id=<?php echo htmlspecialchars($movie['id']); ?>" class="btn boutonDetails btn-primary">Voir les détails</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
>>>>>>> 93c2816 (modif)
</body>
</html>
