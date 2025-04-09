<?php
global $conn;
include __DIR__ . '/../../db.php';

header('Content-Type: application/json');

try {
    $query = "SELECT WineryID, WineryName, Website, winery_lat, winery_lon FROM descriptifs";
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