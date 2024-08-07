<?php
function fetchMoviesFromApi() {
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://api.themoviedb.org/3/movie/top_rated?language=en-US&page=1",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI5MzYwOTQzZDU1NGZlNTg3N2Y2YjcwZDZkMDNiYjZhYSIsIm5iZiI6MTcyMjYwNjc2OS45NTU2MDMsInN1YiI6IjY2YWNlM2U5OWQ1OTFhZDA0MGQyZTc5MiIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.kGdnLpXJprFaL4OKUApmNyqsV8LiF42IGeU-yPAjvqE",
            "accept: application/json"
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
        return null;
    } else {
        return json_decode($response, true); // Convertir la réponse JSON en tableau associatif PHP
    }
}
?>
