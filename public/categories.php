<?php
global $pdo;
session_start();
require_once('../includes/db_connect.php');
$base_image_url = 'https://image.tmdb.org/t/p/w500';

<<<<<<< HEAD
=======
//Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['role'])) {
    die("Accès refusé");
}

>>>>>>> cffee3845227eaa4d4e51ff9619a0a1c2273411f
// Liste des genres autorisés
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

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
            // Récupérer les films pour chaque genre avec une correspondance exacte
            $query = "SELECT * FROM movies WHERE FIND_IN_SET(:genre, movies_genres) > 0";
            $stmt = $pdo->prepare($query);
            $stmt->execute([':genre' => $genre]);
            $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <?php foreach ($movies as $movie): ?>
                <div class="movie">
                    <img src="<?php echo htmlspecialchars($base_image_url . htmlspecialchars($movie['movies_image'])); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                    <a href="movie.php?id=<?php echo htmlspecialchars($movie['id']); ?>" class="text-align-center"><?php echo htmlspecialchars($movie['title']); ?></a>
                </div>
            <?php endforeach; ?>
        </div>
        <button class="nav-arrow right" onclick="scrollRight('<?php echo htmlspecialchars($genre); ?>')">&#9654;</button>
    </div>
<?php endforeach; ?>

<script>
    function scrollLeft(categoryId) {
        var container = document.getElementById('container-' + categoryId);
        container.scrollBy({ left: -1500, behavior: 'smooth' });
    }

    function scrollRight(categoryId) {
        var container = document.getElementById('container-' + categoryId);
        container.scrollBy({ left: 1500, behavior: 'smooth' });
    }
</script>
</body>
</html>
