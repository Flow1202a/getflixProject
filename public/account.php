<?php
global $pdo;
session_start();
require_once('../includes/db_connect.php');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ../public/logintest.php');
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

// Suppression des messages
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];

    // Récupérer le message pour vérifier les droits de suppression
    $query = "SELECT user_id FROM messages WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id' => $delete_id]);
    $message = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($message) {
        if ($user_role == 'admin' || $message['user_id'] == $user_id) {
            $delete_query = "DELETE FROM messages WHERE id = :id";
            $stmt = $pdo->prepare($delete_query);
            $stmt->execute(['id' => $delete_id]);

            header('Location: ../includes/back_office.php');
            exit;
        } else {
            echo "Vous n'avez pas les permissions nécessaires pour supprimer ce message.";
        }
    } else {
        echo "Message introuvable.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon compte</title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/accountStyle.css">
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

<div class="container">
    <h1>Mon compte</h1>

    <!-- Section des films favoris -->
    <h2>Mes favoris</h2>
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
    <h2>Gestion des messages</h2>
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
                    <td>
                        <?php if ($user_role == 'admin' || $message['username'] == $_SESSION['username']): ?>
                            <a href="../includes/back_office.php?delete=<?php echo $message['id']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce message ?');">Supprimer</a>
                        <?php else: ?>
                            <span>Non autorisé</span>
                        <?php endif; ?>
                    </td>
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
<script src="../javascript/script.js" defer></script>
</body>
</html>