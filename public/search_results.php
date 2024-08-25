<?php
global $pdo;
session_start();
require_once('../includes/db_connect.php');
$base_image_url = 'https://image.tmdb.org/t/p/w500';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['role'])) {
    die("Accès refusé");
}

// Récupérer le terme de recherche depuis la requête
$search_term = isset($_GET['q']) ? trim($_GET['q']) : '';

if ($search_term === '') {
    die("Veuillez entrer un terme de recherche.");
}

// Normaliser le terme de recherche en remplaçant les tirets par des espaces
$search_term_normalized = str_replace(['-', '_'], ' ', $search_term);

// Préparer la requête pour chercher dans les titres de films, les noms d'artistes et les genres
$query = "
    SELECT * FROM movies 
    WHERE REPLACE(title, '-', ' ') LIKE :search_term 
    OR REPLACE(artist_name, '-', ' ') LIKE :search_term 
    OR FIND_IN_SET(:search_term_exact, REPLACE(movies_genres, '-', ' '))
";
$stmt = $pdo->prepare($query);
$search_param = "%$search_term_normalized%";
$search_param_exact = $search_term_normalized;
$stmt->execute([
    ':search_term' => $search_param,
    ':search_term_exact' => $search_param_exact
]);

$movies = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="fr">
<link rel="stylesheet" href="../style/style.css">

<head>
    <meta charset="UTF-8">
    <title>Résultats de Recherche</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #141414;
            color: #ffffff;
        }
        header {
            padding: 20px;
            background-color: #000;
            text-align: center;
        }
        .movie {
            width: 200px;
            margin: 15px;
            text-align: center;
        }
        .movie img {
            width: 100%;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Résultats pour "<?php echo htmlspecialchars($search_term); ?>"</h1>
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
                        <a href="movie.php?id=<?php echo htmlspecialchars($movie['id']); ?>" class="btn boutonDetails btn-primary">Voir les détails</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
