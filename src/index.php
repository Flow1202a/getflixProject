<?php
    require 'vendor/autoload.php';

    use GuzzleHttp\Client;

    $client = new Client();
    $response = $client->request('GET', 'https://api.themoviedb.org/3/movie/popular', [
        'query' => [
            'api_key' => '35deb6f78ce3b66bbf5aa3b3a40ef939'
        ]
    ]);

    $body = $response->getBody();
    $data = json_decode($body, true);

    foreach ($data['results'] as $movie) {
        echo $movie['title'] . '<br>';
    }
