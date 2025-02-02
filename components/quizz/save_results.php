<?php
session_start();
require_once('../../db.php');
global $conn;
header('Content-Type: application/json');

// Vérifie si l'utilisateur est bien connecté
if (!isset($_SESSION['user']['id'])) {
    echo json_encode(['error' => 'Utilisateur non connecté']);
    exit;
}

$userId = $_SESSION['user']['id']; // Récupération de l'ID utilisateur depuis la session

// Récupère les données envoyées par JavaScript
$data = json_decode(file_get_contents('php://input'), true);

// Log les données reçues pour débogage
file_put_contents('debug_log.txt', "Données reçues : " . print_r($data, true) . "\n", FILE_APPEND);

// Vérifie si les données sont valides
if (!$data || !is_array($data)) {
    echo json_encode(['error' => 'Données invalides reçues']);
    exit;
}

// Colonnes valides pour la base de données
$validColumns = ['agrumes', 'floral', 'fruit_rouge', 'boisé', 'fruit_noir'];

// Initialisation des résultats avec des valeurs par défaut
$quizResults = array_fill_keys($validColumns, null);

// Remplit les valeurs avec les données envoyées
foreach ($data as $key => $value) {
    if (in_array($key, $validColumns)) {
        $quizResults[$key] = ($value === 'oui') ? 'oui' : 'non';
    }
}

try {
    // Préparation de la requête SQL
    $sql = "REPLACE INTO quiz_results (user_id, agrumes, floral, fruit_rouge, boisé, fruit_noir, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Erreur de préparation de la requête : " . $conn->error);
    }

    // Liaison des paramètres et exécution
    $stmt->bind_param("isssss", 
        $userId, 
        $quizResults['agrumes'], 
        $quizResults['floral'], 
        $quizResults['fruit_rouge'], 
        $quizResults['boisé'], 
        $quizResults['fruit_noir']
    );

    if (!$stmt->execute()) {
        throw new Exception("Erreur lors de l'exécution de la requête : " . $stmt->error);
    }

    $stmt->close();
    echo json_encode(['success' => 'Résultats enregistrés avec succès.']);

} catch (Exception $e) {
    // Log l'erreur dans le fichier debug
    file_put_contents('debug_log.txt', "Erreur SQL : " . $e->getMessage() . "\n", FILE_APPEND);
    echo json_encode(['error' => $e->getMessage()]);
}

$conn->close(); // Ferme la connexion proprement
?>
