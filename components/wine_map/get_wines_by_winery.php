<?php
/*
    Retourne en JSON les vins d'un domaine donné (via son WineryID).

    Contenu :
    - Jointure descriptifs + scrap pour infos complètes (nom, type, prix, visuel).
    - Filtrage par WineryID.

    Utilisation :
    - Appelé depuis map-main.js quand on clique sur un marqueur.
    - Bdd : tables `descriptifs`, `scrap`
*/

include '../../db.php';

header('Content-Type: application/json');

if (!isset($_GET['winery_id']) || !is_numeric($_GET['winery_id'])) {
    echo json_encode(["error" => "Invalid Winery ID"]);
    exit();
}

$winery_id = intval($_GET['winery_id']);

try {
    $query = "
        SELECT d.idwine, d.NameWine_WithWinery, d.Type, s.thumb, s.Price
        FROM descriptifs d
        LEFT JOIN scrap s ON d.idwine = s.idwine
        WHERE d.WineryID = ?
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $winery_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $wines = $result->fetch_all(MYSQLI_ASSOC);



    echo json_encode($wines ?: [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>
