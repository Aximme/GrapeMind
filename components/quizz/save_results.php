<?php
session_start();
require_once('../../db.php');
global $conn;
header('Content-Type: application/json');

// Log les données reçues pour débogage
$data = json_decode(file_get_contents('php://input'), true);
file_put_contents('debug_log.txt', print_r($data, true), FILE_APPEND);

if (!isset($data['user_id'])) {
    echo json_encode(['error' => 'ID utilisateur manquant']);
    exit;
}

$validColumns = ['agrumes', 'floral', 'fruit_rouge', 'boisé', 'fruit_noir'];
$quizResults = array_fill_keys($validColumns, null);

foreach ($data as $key => $value) {
    if (in_array($key, $validColumns)) {
        $quizResults[$key] = $value === 'oui' ? 'oui' : 'non';
    }
}

$userId = $data['user_id'];

try {
    $sql = "REPLACE INTO quiz_results (user_id, agrumes, floral, fruit_rouge, boisé, fruit_noir, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssss", $userId, $quizResults['agrumes'], $quizResults['floral'],
        $quizResults['fruit_rouge'], $quizResults['boisé'], $quizResults['fruit_noir']);
    $stmt->execute();
    $stmt->close();

    echo json_encode(['success' => 'Résultats enregistrés avec succès.']);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
$conn->close();
?>
