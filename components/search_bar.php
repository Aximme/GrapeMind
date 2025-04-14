<!--
    Affiche un champ de recherche avec des suggestions dynamiques.

    Contenu :
    - Intègre un champ `input` avec retour AJAX via search_bar_server.php.
    - Redirige vers search_results.php après envoie de la requête.

    Dépendances :
    - JS : /js/search_bar.js
    - CSS : /css/search_bar.css
    - API : wine_map/search_bar_server.php
-->


<?php include __DIR__ . '/wine_map/search_bar_server.php';
include __DIR__ . '/../db.php';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1, width=device-width">
    <link rel="stylesheet" href="/GrapeMind/css/search_bar.css" />
    <title>Recherche de vin</title>
</head>
<body>

<div class="search-box">
    <form action="/GrapeMind/components/search_results.php" method="get">
        <button type="submit" class="search-icon" aria-label="Rechercher"></button>
        <input type="text" name="query" class="search-form" placeholder="Rechercher un vin..." onkeyup="fetchSuggestions(this.value)">
    </form>
    <div id="search-suggestions" class="search-suggestions"></div>
</div>

<script src="/GrapeMind/js/search_bar.js" defer></script>
</body>
</html>