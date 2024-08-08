<?php
require_once('../includes/db_connect.php');

$idMovie = $_GET['id'];

// URL de la base pour les images
$base_image_url = 'https://image.tmdb.org/t/p/w500';

// URL de la base pour les images
$base_trailer = 'https://www.youtube.com/embed/';

    // Prepare a secure SQL statement
    $sql = "SELECT * FROM movies WHERE movies.id = :idMovie";
    $stmt = $pdo->prepare($sql);

    // Bind parameters to prevent SQL injection
    $stmt->bindParam(':idMovie', $idMovie, PDO::PARAM_STR);

    // Execute the prepared statement
    $stmt->execute();
    $movie = $stmt->fetch(PDO::FETCH_ASSOC);
    // VERIFIE SI MOVIES EST PAS VIDE
    if(!empty($movie)){
        // SI MOVIE PAS VIDE ON FAIT CE QUE ON A FAIRE
  //      $movie = json_encode($movie, JSON_UNESCAPED_UNICODE);
        var_dump($movie);
    } else {
        // PAS DE MOVIE SOIT ON AFFICHE MESSAGE INEXISTANT SOIT REDIRECTION VERS INDEX
        echo 'existe pas';
    }
?>

<h1>
    <?php 
    echo $movie['title'];?>
</h1>

<div class="overview">
<?php 
echo $movie['overview'];
?>
</div>

<div class="movieImage">
    <img src="<?php echo htmlspecialchars($base_image_url . ($movie['movies_image']));?>"
</div>

<div>
    <iframe class="trailer" width="420" height="315" src="<?php echo ($base_trailer . ($movie['movies_trailer']));?>"></iframe>
    <style> 
        .trailer {
            position: none;
            
        }
    </style>
</div>