<?php include '../header.php'; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carte de France</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="/css/map/map-main.css">


    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css" />
    <script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster-src.js"></script>
</head>
<body>
<div id="content">
    <div id="map"></div>
    <div id="info-panel">
        <select id="region-select">
            <option value="">-- Sélectionnez une région --</option>
        </select>
        <div id="region-details">
            <h2>Informations sur la région</h2>
            <div class="info-card" id="region-info">
                <p>🗺️ Sélectionnez une région dans le menu déroulant pour voir ses détails.</p>
            </div>
            <div id="wine-list"></div> <!-- Zone pour afficher les vins -->
        </div>
    </div>
</div>


<script src="/js/map/map-main.js"></script>
</body>
</html>