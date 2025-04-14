<?php
/*
    Retourne en JSON les coordonnées + infos des domaines présents dans la bdd.

    Contenu :
    - Sélectionne les domaines avec coordonnées GPS définies.
    - Regroupe par WineryID avec les valeurs les + récentes.

    Utilisation :
    - Appelé avec AJAX via la carte (map-main.js) pour placer les marqueurs.
    - Bdd : table `descriptifs`
*/

global $conn;
include '../../db.php';

header('Content-Type: application/json');

try {
    $query = "
            SELECT WineryID, 
               MAX(WineryName) AS WineryName, 
               MAX(Website) AS Website, 
               MAX(winery_lat) AS winery_lat, 
               MAX(winery_lon) AS winery_lon
        FROM descriptifs
        WHERE winery_lat IS NOT NULL AND winery_lon IS NOT NULL
        GROUP BY WineryID
    ";
    $stmt = $conn->prepare($query);
    $stmt->execute();

    $result = $stmt->get_result();
    $wineries = [];

    while ($row = $result->fetch_assoc()) {
        $wineries[] = [
            'WineryID' => $row['WineryID'],
            'WineryName' => $row['WineryName'],
            'Website' => $row['Website'],
            'winery_lat' => $row['winery_lat'],
            'winery_lon' => $row['winery_lon']
        ];
    }

    echo json_encode($wineries, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>