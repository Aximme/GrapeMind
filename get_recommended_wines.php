<?php
require_once __DIR__ . '/db.php';

if (!isset($conn)) {
    die(json_encode(["error" => "Erreur de connexion à la base de données."]));
}

$file_path = "components/quizz/recommendations.json";
$json_content = file_get_contents($file_path);
$recommendations = json_decode($json_content, true);

if (!$recommendations || !isset($recommendations["recommendations"])) {
    echo json_encode([]);
    exit;
}

$wineIds = array_column($recommendations["recommendations"], "idwine");

if (empty($wineIds)) {
    echo json_encode([]);
    exit;
}

$placeholders = implode(",", array_fill(0, count($wineIds), "?"));
$query = "SELECT idwine, thumb FROM scrap WHERE idwine IN ($placeholders)";
$stmt = $conn->prepare($query);

$types = str_repeat("i", count($wineIds));
$stmt->bind_param($types, ...$wineIds);
$stmt->execute();

$result = $stmt->get_result();
$thumbs = $result->fetch_all(MYSQLI_ASSOC);

$thumbMap = [];
foreach ($thumbs as $thumb) {
    $thumbMap[$thumb["idwine"]] = $thumb["thumb"];
}

foreach ($recommendations["recommendations"] as &$wine) {
    $wine["thumb"] = $thumbMap[$wine["idwine"]] ?? "default.jpg";
}

header("Content-Type: application/json");
echo json_encode($recommendations["recommendations"]);
?>
