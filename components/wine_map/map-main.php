<?php include '../header.php'; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carte de France</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="/GrapeMind/css/map/map-main.css">


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
            <div class="info-card">
                <h2>🇫🇷 Aucune région sélectionnée</h2>
                <p>🗺️ Sélectionnez une région dans le menu déroulant pour voir ses détails.</p>
            </div>
        </div>
    </div>
</div>

<script src="/GrapeMind/js/map/map-main.js"></script>
</body>
</html>