<?php
include __DIR__ . '/../../db.php';
global $conn;

$query = isset($_GET['query']) ? $_GET['query'] : '';

if (!empty($query)) {
    // Requête pour récupérer les résultats avec les informations nécessaires (par exemple, l'image du vin)
    $sql = "SELECT name, thumb,idwine FROM scrap WHERE name LIKE '%$query%' LIMIT 4";
    $result = $conn->query($sql);

    $suggestions = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $suggestions[] = $row;
        }
    }

    // Retourne les suggestions au format JSON
    header('Content-Type: application/json');
    echo json_encode($suggestions);
}


if (isset($conn)) {
    $conn->close();
}
