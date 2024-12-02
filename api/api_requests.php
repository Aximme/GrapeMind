
<?php

if (!defined('DIFFBOT_TOKEN')) {
    define('DIFFBOT_TOKEN', '7e9f601eb35483a23b1dea36d0b7f3be');
}

if (!defined('DIFFBOT_API_BASE')) {
    define('DIFFBOT_API_BASE', 'https://api.diffbot.com/v3/analyze?');
}
function getWineEvents()
{
    $url = 'https://www.visitfrenchwine.com/events.html';
    $apiUrl = DIFFBOT_API_BASE . 'url=' . urlencode($url) . '&token=' . DIFFBOT_TOKEN;

    // Initialisation de la requête cURL
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Erreur cURL : ' . curl_error($ch);
        return null;
    }

    curl_close($ch);

    // Décoder la réponse JSON
    $decodedResponse = json_decode($response, true);

    // Préparer un tableau JSON valide
    $file = __DIR__ . '/wine_events.json';
    if (isset($decodedResponse['objects'][0]['items'])) {
        $events = $decodedResponse['objects'][0]['items'];

        // Sauvegarder les événements dans un tableau JSON unique
        file_put_contents($file, json_encode($events, JSON_PRETTY_PRINT));
    } else {
        // Si aucun événement n'est trouvé, sauvegarder la réponse brute
        file_put_contents($file, json_encode($decodedResponse, JSON_PRETTY_PRINT));
    }

    return $decodedResponse;
}
