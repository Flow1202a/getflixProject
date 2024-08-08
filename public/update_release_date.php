<!-- Ici, on va faire un script qui va permettre d'introduire dans la BDD la date de sortie du film  -->
<?php
global $pdo;
require_once '../includes/db_connect.php'; // Assurez-vous que le chemin est correct

// Remplacez cette clé par votre propre clé API valide
$api_key = 'eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiIzODVlMjM2MjVlN2IxNzA4Y2U2YzQxZjY5YWEwMTQyOSIsIm5iZiI6MTcyMzAzODYwNi40MzA5MjksInN1YiI6IjY2YjM3YWY0MzIzZDY3ZjhkMDkyZTNhMiIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.gDi0Los2cvzfAdxLkIA_vPly75nnvchTZKnPDlM7H68';
