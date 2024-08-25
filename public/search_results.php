<?php
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
    <link rel="stylesheet" href="../style/categorieStyle.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>

<header>
    <nav class="nav position-fixed w-100">
        <a href="index.php" class="logo">GetFlixDeNullos</a>
        <ul class="nav-links align-items-center">
            <li><a href="index.php">Home</a></li>
            <li><a href="categories.php">Categories</a></li>
            <li><a href="#">WatchList</a></li>
            <li><a href="../public/account.php">Account</a></li>
            <?php if (isset($_SESSION['role'])): ?>
                <li><a href="../includes/logout.php">Se déconnecter</a></li>
            <?php else: ?>
                <li><a href="logintest.php">Connexion</a></li>
            <?php endif; ?>
        </ul>

        <div class="search-box">
            <form action="search_results.php" method="get">
                <input class="searchTarget" type="text" name="q" placeholder="Rechercher un film, un artiste, ou un genre..." value="<?php echo htmlspecialchars($search_term); ?>" />
                <button type="submit" style="display: none;"></button>
            </form>
        </div>
    </nav>
</header>

<div class="movies-container" style="padding-top: 80px;">
    <?php if (empty($movies)): ?>
        <p>Aucun résultat trouvé pour "<?php echo htmlspecialchars($search_term); ?>"</p>
    <?php else: ?>
        <?php foreach ($movies as $movie): ?>
            <div class="movie align-items-center">
                <img src="<?php echo htmlspecialchars($base_image_url . htmlspecialchars($movie['movies_image'])); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                <a href="movie.php?id=<?php echo htmlspecialchars($movie['id']); ?>"><?php echo htmlspecialchars($movie['title']); ?></a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>
