<?php include '../header.php'; ?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carte de France</title>
    <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/map.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/geodata/franceDepartmentsLow.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
    <link rel="stylesheet" href="/GrapeMind/css/map/map-main.css">

</head>
<body>

<div id="content">
    <div id="map"></div>
    <div id="info-panel">
        <div class="info-card">
            <h2>Carte Informations sur les Domaines</h2>
            <p>Cliquez sur une région pour en savoir plus sur ses vins et les domaines qui les produisent. Vous trouverez des informations sur les régions viticoles, les types de vins qui y sont produits, les domaines qui les produisent, ainsi que des conseils pour les découvrir.</p>
        </div>
    </div>
</div>

    <script src="/GrapeMind/js/map/map-main.js"></script>
</body>
</html>