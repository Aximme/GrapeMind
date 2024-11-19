<?php
global $conn;

// Affiche les erreurs pour débogage (désactivez en production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include __DIR__ . '/../../db.php';

header('Content-Type: application/json'); // Retour JSON

ob_start(); // Commence un tampon de sortie

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['results']) || !isset($data['user_id'])) {
    ob_end_clean(); // Vide le tampon
    echo json_encode(['error' => 'Données JSON invalides ou absentes.']);
    exit;
}

$results = json_encode($data['results']); // Encode les résultats en JSON
$userId = $data['user_id'];

// Étape 1 : Supprimer les résultats précédents de l'utilisateur, s'ils existent
$delete_sql = "DELETE FROM quiz_results WHERE user_id = ?";
$stmt = $conn->prepare($delete_sql);
if (!$stmt) {
    ob_end_clean(); // Vide le tampon
    echo json_encode(['error' => 'Erreur dans la préparation de la requête pour suppression : ' . $conn->error]);
    exit;
}
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->close();

// Étape 2 : Insérer les nouveaux résultats du quiz
$insert_sql = "INSERT INTO quiz_results (user_id, results, created_at) VALUES (?, ?, NOW())";
$stmt = $conn->prepare($insert_sql);
if (!$stmt) {
    ob_end_clean(); // Vide le tampon
    echo json_encode(['error' => 'Erreur dans la préparation de la requête pour insertion : ' . $conn->error]);
    exit;
}
$stmt->bind_param("is", $userId, $results);

if (!$stmt->execute()) {
    ob_end_clean(); // Vide le tampon
    echo json_encode(['error' => 'Erreur lors de l\'insertion : ' . $stmt->error]);
    exit;
}

$stmt->close();
$conn->close();

ob_end_clean(); // Vide le tampon avant de retourner le JSON
echo json_encode(['success' => 'Résultats enregistrés avec succès.']);
?>
