<?php
session_start();
global $conn;
header('Content-Type: application/json');

// Inclure la connexion à la base de données
require_once '../../db.php';

if (!isset($conn)) {
    echo json_encode(["error" => "Erreur de connexion à la base de données."]);
    exit();
}

// Récupérer et décoder les données JSON
$data = json_decode(file_get_contents("php://input"), true);

// Debugging : log des données reçues
file_put_contents("debug_log.txt", print_r($data, true));

if (!$data || !isset($data["responses"]) || !is_array($data["responses"])) {
    echo json_encode(["error" => "Données invalides"]);
    exit();
}

$user_id = $_SESSION['user_id'] ?? 0; // Mettre 0 si l'utilisateur n'est pas connecté

// Initialisation des réponses
$answers = [
    "question1" => null,
    "question2" => null,
    "question3" => null,
    "question4" => null
];

// Récupération des réponses du JSON
foreach ($data["responses"] as $response) {
    if (!isset($response["question_id"]) || !is_numeric($response["question_id"])) {
        echo json_encode(["error" => "ID de question invalide"]);
        exit();
    }
    if (!isset($response["answers"]) || !is_array($response["answers"])) {
        echo json_encode(["error" => "Format de réponse invalide"]);
        exit();
    }

    $question_id = intval($response["question_id"]);
    $answer_text = htmlspecialchars(trim(implode(", ", $response["answers"])), ENT_QUOTES, 'UTF-8');

    // Assigner la réponse à la bonne question
    if ($question_id >= 1 && $question_id <= 4) {
        $answers["question{$question_id}"] = $answer_text;
    }
}

// Préparer la requête pour insérer ou mettre à jour les réponses
$stmt = $conn->prepare("
    INSERT INTO quiz_answers (user_id, question1_answer, question2_answer, question3_answer, question4_answer, updated_at)
    VALUES (?, ?, ?, ?, ?, NOW())
    ON DUPLICATE KEY UPDATE 
        question1_answer = VALUES(question1_answer),
        question2_answer = VALUES(question2_answer),
        question3_answer = VALUES(question3_answer),
        question4_answer = VALUES(question4_answer),
        updated_at = NOW()
");

if (!$stmt) {
    echo json_encode(["error" => "Erreur lors de la préparation de la requête : " . $conn->error]);
    exit();
}

// Lier les paramètres avec bind_param
$stmt->bind_param(
    "issss",
    $user_id,
    $answers["question1"],
    $answers["question2"],
    $answers["question3"],
    $answers["question4"]
);

// Exécuter la requête et vérifier le résultat
if ($stmt->execute()) {
    echo json_encode(["message" => "Les réponses ont été enregistrées ou mises à jour avec succès."]);
} else {
    echo json_encode(["error" => "Erreur SQL : " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
