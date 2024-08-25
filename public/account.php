<?php
global $pdo;
session_start();
require_once('../includes/db_connect.php');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: logintest.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

// Récupérer les films favoris
if ($user_role == 'admin') {
    // L'administrateur voit les favoris de tous les utilisateurs
    $query = "SELECT movies.id, movies.title, movies.movies_image, users.username 
              FROM movies 
              JOIN user_movies ON movies.id = user_movies.movie_id 
              JOIN users ON user_movies.user_id = users.id 
              WHERE user_movies.favorite = 1";
} else {
    // L'utilisateur normal voit uniquement ses propres favoris
    $query = "SELECT movies.id, movies.title, movies.movies_image 
              FROM movies 
              JOIN user_movies ON movies.id = user_movies.movie_id 
              WHERE user_movies.user_id = :user_id AND user_movies.favorite = 1";
}

$stmt = $pdo->prepare($query);
if ($user_role != 'admin') {
    $stmt->execute(['user_id' => $user_id]);
} else {
    $stmt->execute();
}
$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les messages
if ($user_role == 'admin') {
    // L'administrateur voit tous les messages
    $query = "SELECT messages.id, users.username, messages.content, messages.created_at 
              FROM messages 
              JOIN users ON messages.user_id = users.id";
} else {
    // L'utilisateur normal voit uniquement ses propres messages
    $query = "SELECT messages.id, users.username, messages.content, messages.created_at 
              FROM messages 
              JOIN users ON messages.user_id = users.id 
              WHERE user_id = :user_id";
}

$stmt = $pdo->prepare($query);
if ($user_role != 'admin') {
    $stmt->execute(['user_id' => $user_id]);
} else {
    $stmt->execute();
}
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Compte</title>
    <link rel="stylesheet" href="../style/style.css">
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
            <li><a href="account.php">Account</a></li>
            <li><a href="logintest.php">Connexion</a></li>
        </ul>
    </nav>
</header>

<div class="container">
    <h1>Mon Compte</h1>

    <!-- Section des films favoris -->
    <h2>Mes Favoris</h2>
    <div class="favorites-slider">
        <?php if (!empty($favorites)): ?>
            <?php foreach ($favorites as $movie): ?>
                <div class="favorite-item">
                    <a href="movie.php?id=<?php echo $movie['id']; ?>">
                        <img src="https://image.tmdb.org/t/p/w500<?php echo $movie['movies_image']; ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                        <p><?php echo htmlspecialchars($movie['title']); ?></p>
                    </a>
                    <?php if ($user_role == 'admin'): ?>
                        <p><em>Ajouté par : <?php echo htmlspecialchars($movie['username']); ?></em></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Vous n'avez aucun film dans vos favoris.</p>
        <?php endif; ?>
    </div>

    <!-- Section Back-Office -->
    <h2>Gestion des Messages</h2>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Utilisateur</th>
            <th>Message</th>
            <th>Date de Création</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($messages)): ?>
            <?php foreach ($messages as $message): ?>
                <tr>
                    <td><?php echo htmlspecialchars($message['id']); ?></td>
                    <td><?php echo htmlspecialchars($message['username']); ?></td>
                    <td><?php echo htmlspecialchars($message['content']); ?></td>
                    <td><?php echo htmlspecialchars($message['created_at']); ?></td>
                    <td><a href="../includes/back_office.php?delete=<?php echo $message['id']; ?>">Supprimer</a></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">Aucun message trouvé.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="../javascript/scriptAccoount.js"></script>
</body>
</html>
