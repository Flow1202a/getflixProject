<?php
global $pdo;
session_start();
require_once('../includes/db_connect.php');

$user_id = $_SESSION['user_id'] ?? null;
$movie_id = $_POST['movie_id'] ?? null;

if ($user_id && $movie_id) {
    // Vérifier si le film est déjà dans les favoris
    $query = "SELECT * FROM user_movies WHERE user_id = :user_id AND movie_id = :movie_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['user_id' => $user_id, 'movie_id' => $movie_id]);
    $favorite_entry = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($favorite_entry) {
        // Inverser l'état du favori
        $new_favorite_state = $favorite_entry['favorite'] ? 0 : 1;
        $query = "UPDATE user_movies SET favorite = :favorite WHERE user_id = :user_id AND movie_id = :movie_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['favorite' => $new_favorite_state, 'user_id' => $user_id, 'movie_id' => $movie_id]);
    } else {
        // Ajouter le film aux favoris
        $query = "INSERT INTO user_movies (user_id, movie_id, favorite) VALUES (:user_id, :movie_id, 1)";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['user_id' => $user_id, 'movie_id' => $movie_id]);
    }
}

header("Location: movie.php?id=$movie_id");
exit;
