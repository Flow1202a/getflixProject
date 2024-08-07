<?php
require_once '../includes/db_connect.php'; 

// URL de la base pour les images
$base_image_url = 'https://image.tmdb.org/t/p/w500'; 

// Requête pour obtenir les films triés par rating
$query = "SELECT id, title, movies_trailer, movies_image, overview, rating FROM movies ORDER BY rating DESC";
$stmt = $pdo->query($query);
$movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Films les Mieux Notés</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .movie {
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 20px;
            display: flex;
            align-items: center;
        }
        .movie img {
            max-width: 150px;
            height: auto;
            border-radius: 8px;
            margin-right: 20px;
        }
        .movie-details {
            flex: 1;
        }
        .movie-title {
            font-size: 1.5em;
            margin: 0;
            color: #333;
        }
        .movie-description {
            margin: 10px 0;
            color: #555;
        }
        .movie-rating {
            margin: 5px 0;
            color: #888;
        }
        .trailer-button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 1em;
            color: #fff;
            background-color: #007BFF;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .trailer-button:hover {
            background-color: #0056b3;
        }
        .no-trailer {
            color: #888;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Liste des Films les Mieux Notés</h1>
        <?php foreach ($movies as $movie): ?>
            <div class="movie">
                <?php if (!empty($movie['movies_image'])): ?>
                    <img src="<?php echo htmlspecialchars($base_image_url . htmlspecialchars($movie['movies_image'])); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                <?php endif; ?>
                <div class="movie-details">
                    <div class="movie-title"><?php echo htmlspecialchars($movie['title']); ?></div>
                    <div class="movie-description"><?php echo htmlspecialchars($movie['overview']); ?></div>
                    <div class="movie-rating">Note: <?php echo htmlspecialchars($movie['rating']); ?>/10</div>
                    <?php if (!empty($movie['movies_trailer'])): ?>
                        <a class="trailer-button" href="https://www.youtube.com/watch?v=<?php echo htmlspecialchars($movie['movies_trailer']); ?>" target="_blank">Voir le Trailer</a>
                    <?php else: ?>
                        <div class="no-trailer">Aucun trailer disponible pour ce film.</div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
