<?php
session_start();  // Assurez-vous que la session est démarrée
global $pdo;
require_once '../includes/db_connect.php';

// URL de la base pour les images
$base_image_url = 'https://image.tmdb.org/t/p/w500';

// Requête pour obtenir les films triés par rating
$query = "SELECT id, title, movies_trailer, movies_image, overview, rating FROM movies ORDER BY rating DESC";
$stmt = $pdo->query($query);
$movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../style/style.css">
    <script src="../javascript/script.js" defer></script>
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Welcome</title>
</head>
<body>

<header>
    <nav class="nav position-absolute">
        <i class="uil uil-bars navOpenBtn"></i>
        <a href="#" class="logo">GetFlixDeNullos</a>

        <ul class="nav-links align-items-center">
            <i class="uil uil-times navCloseBtn"></i>
            <li><a href="index.php">Home</a></li>
            <li><a href="#">Categories</a></li>
            <li><a href="#">WatchList</a></li>
            <li><a href="../includes/back_office.php">Account</a></li>
            <?php if (isset($_SESSION['role'])): ?>
                <li><a href="../includes/logout.php">Se déconnecter</a></li>
            <?php else: ?>
                <li><a href="logintest.php">Connexion</a></li>
            <?php endif; ?>
        </ul>

        <i class="uil uil-search search-icon" id="searchIcon"></i>
        <div class="search-box">
            <i class="uil uil-search search-icon"></i>
            <input type="text" placeholder="Search here..." />
        </div>
    </nav>
</header>

<article class="container mb-5 mt-5">
    <div class="row flex-wrap d-flex justify-content-between align-content-center">
        <?php foreach ($movies as $movie): ?>
            <div class="card moviecarte col-xs-12 col-sm-6 col-md-3 col-lg-2 mb-5 me-1">
                <img src="<?php echo htmlspecialchars($base_image_url . htmlspecialchars($movie['movies_image'])); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                <div class="card-body align-content-center">
                    <h5 class="card-title" style="color: #EED6D3"><?php echo htmlspecialchars($movie['title']); ?></h5>
                    <a href="movie.php?id=<?php echo htmlspecialchars($movie['id']); ?>" class="btn boutonDetails btn-primary">Voir les détails</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</article>

<section class="footer">
    <!-- Reste de ton code... -->
</section>

</body>
</html>
