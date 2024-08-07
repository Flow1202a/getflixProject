<?php
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
            <li><a href="#">Home</a></li>
            <li><a href="#">Categories</a></li>
            <li><a href="#">WatchList</a></li>
            <li><a href="#">Account</a></li>
            <li><a href="#">Connexion</a></li>
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
            <div class="card col-sm-1 col-md-2 col-lg-2 mb-5 me-1" style="">
                <img src="<?php echo htmlspecialchars($base_image_url . htmlspecialchars($movie['movies_image'])); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($movie['title']); ?></h5>
                    <a href="movie_details.php?id=<?php echo htmlspecialchars($movie['id']); ?>" class="btn btn-primary">Voir les détails</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</article>

<section class="footer">
    <div class="footer-row">
        <div class="footer-col">
            <h4>Info</h4>
            <ul class="links">
                <li><a href="#">About Us</a></li>
                <li><a href="#">Compressions</a></li>
                <li><a href="#">Customers</a></li>
                <li><a href="#">Service</a></li>
                <li><a href="#">Collection</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>Explore</h4>
            <ul class="links">
                <li><a href="#">Free Designs</a></li>
                <li><a href="#">Latest Designs</a></li>
                <li><a href="#">Themes</a></li>
                <li><a href="#">Popular Designs</a></li>
                <li><a href="#">Art Skills</a></li>
                <li><a href="#">New Uploads</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>Legal</h4>
            <ul class="links">
                <li><a href="#">Customer Agreement</a></li>
                <li><a href="#">Privacy Policy</a></li>
                <li><a href="#">GDPR</a></li>
                <li><a href="#">Security</a></li>
                <li><a href="#">Testimonials</a></li>
                <li><a href="#">Media Kit</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>Newsletter</h4>
            <p>
                Subscribe to our newsletter for a weekly dose
                of news, updates, helpful tips, and
                exclusive offers.
            </p>
            <form action="#">
                <input type="text" placeholder="Your email" required>
                <button type="submit">SUBSCRIBE</button>
            </form>
            <div class="icons">
                <i class="fa-brands fa-facebook-f"></i>
                <i class="fa-brands fa-twitter"></i>
                <i class="fa-brands fa-linkedin"></i>
                <i class="fa-brands fa-github"></i>
            </div>
        </div>
    </div>
</section>

</body>
</html>
