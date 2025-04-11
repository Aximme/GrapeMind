<?php
require_once __DIR__ . '/db.php';
session_start();

if (!isset($_SESSION['user'])) {
    $sql = "SELECT name, thumb, price, idwine FROM scrap WHERE price > 2 ORDER BY RAND() LIMIT 15";
} else {
    $user_id = $_SESSION['user']['id'];

    $query = "
        SELECT DISTINCT wr.wine_id, wr.name, wr.price, wr.thumb
        FROM wine_recommendations wr
        WHERE wr.user_id = ?
        ORDER BY wr.price ASC
    ";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die(json_encode(["error" => "Erreur de préparation de la requête SQL."]));
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $wines = $result->fetch_all(MYSQLI_ASSOC);

    if (empty($wines)) {
        echo json_encode([]);
        exit;
    }

    echo json_encode($wines);
    exit;
}

$stmt = $conn->query($sql);
$wines = [];
if ($stmt && $stmt->num_rows > 0) {
    while ($row = $stmt->fetch_assoc()) {
        $wines[] = $row;
    }
}

header("Content-Type: application/json");
echo json_encode($wines);
?>
