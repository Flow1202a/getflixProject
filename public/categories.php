
<?php
session_start();
require_once('../includes/db_connect.php');
global$pdo;
// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['role'])) {
    die("Accès refusé");
}
$base_image_url = 'https://image.tmdb.org/t/p/w500';
// Récupérer tous les genres distincts
$query = "SELECT DISTINCT movies_genres FROM movies";
$stmt = $pdo->query($query);
$allGenres = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Créer un tableau pour stocker chaque genre distinctement
$genres = [];
foreach ($allGenres as $row) {
    $genresArray = explode(',', $row['movies_genres']);
    foreach ($genresArray as $genre) {
        $genre = trim($genre); // Supprimer les espaces éventuels
        if (!in_array($genre, $genres)) {
            $genres[] = $genre;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Catégories de Films</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #141414;
            color: #ffffff;
        }
        header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: #000;
            padding: 10px 0;
            z-index: 1000;
        }
        nav {
            display: flex;
            justify-content: center;
            margin: 0 auto;
            overflow-x: auto;
            white-space: nowrap;
        }
        nav a {
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            font-size: 18px;
        }
        nav a:hover {
            background-color: #444;
        }
        .category {
            margin: 80px 0 20px 0;
            position: relative;
        }
        h2 {
            margin-left: 20px;
        }
        .movies-container {
            display: flex;
            overflow: hidden;
            padding: 20px;
            scroll-behavior: smooth;
        }
        .movie {
            flex: 0 0 auto;
            width: 200px;
            margin-right: 10px;
        }
        .movie img {
            width: 100%;
            border-radius: 5px;
        }
        .nav-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            font-size: 2em;
            color: white;
            background-color: rgba(0, 0, 0, 0.5);
            border: none;
            cursor: pointer;
            padding: 10px;
        }
        .nav-arrow.left {
            left: 0;
        }
        .nav-arrow.right {
            right: 0;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <?php foreach ($genres as $genre): ?>
                <a href="#<?php echo htmlspecialchars($genre); ?>"><?php echo htmlspecialchars($genre); ?></a>
            <?php endforeach; ?>
        </nav>
    </header>

    <?php foreach ($genres as $genre): ?>
        <div class="category" id="<?php echo htmlspecialchars($genre); ?>">
            <h2><?php echo htmlspecialchars($genre); ?></h2>
            <button class="nav-arrow left" onclick="scrollLeft('<?php echo htmlspecialchars($genre); ?>')">&#9664;</button>
            <div class="movies-container" id="container-<?php echo htmlspecialchars($genre); ?>">
                <?php
                // Récupérer les films pour chaque genre
                $query = "SELECT * FROM movies WHERE FIND_IN_SET(:genre, movies_genres)";
                $stmt = $pdo->prepare($query);
                $stmt->execute([':genre' => $genre]);
                $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ?>

                <?php foreach ($movies as $movie): ?>
                    <div class="movie">
                        <img src="<?php echo htmlspecialchars($base_image_url . htmlspecialchars($movie['movies_image'])); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                        <h3><?php echo htmlspecialchars($movie['title']); ?></h3>
                    </div>
                <?php endforeach; ?>
            </div>
            <button class="nav-arrow right" onclick="scrollRight('<?php echo htmlspecialchars($genre); ?>')">&#9654;</button>
        </div>
    <?php endforeach; ?>

    <script>
        function scrollLeft(categoryId) {
            var container = document.getElementById('container-' + categoryId);
            container.scrollBy({ left: -200, behavior: 'smooth' });
        }

        function scrollRight(categoryId) {
            var container = document.getElementById('container-' + categoryId);
            container.scrollBy({ left: 200, behavior: 'smooth' });
        }
    </script>
</body>
</html>
