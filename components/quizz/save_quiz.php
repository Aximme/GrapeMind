<?php
session_start();
global $conn;
header('Content-Type: application/json');

require_once '../../db.php';



if (!isset($conn)) {
    echo json_encode(["error" => "Erreur de connexion à la base de données."]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["error" => "Méthode non autorisée"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data["responses"]) || !is_array($data["responses"])) {
    echo json_encode(["error" => "Données invalides"]);
    exit();
}

$user_id = $_SESSION['user_id'] ?? 0;

$answers = array_fill(1, 15, null);

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

    if ($question_id >= 1 && $question_id <= 15) {
        $answers[$question_id] = $answer_text;
    }
}

$query = "
    INSERT INTO quiz_answers (
        user_id, question1_answer, question2_answer, question3_answer, question4_answer, question5_answer,
        question6_answer, question7_answer, question8_answer, question9_answer, question10_answer,
        question11_answer, question12_answer, question13_answer, question14_answer, question15_answer, updated_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ON DUPLICATE KEY UPDATE
        question1_answer = VALUES(question1_answer),
        question2_answer = VALUES(question2_answer),
        question3_answer = VALUES(question3_answer),
        question4_answer = VALUES(question4_answer),
        question5_answer = VALUES(question5_answer),
        question6_answer = VALUES(question6_answer),
        question7_answer = VALUES(question7_answer),
        question8_answer = VALUES(question8_answer),
        question9_answer = VALUES(question9_answer),
        question10_answer = VALUES(question10_answer),
        question11_answer = VALUES(question11_answer),
        question12_answer = VALUES(question12_answer),
        question13_answer = VALUES(question13_answer),
        question14_answer = VALUES(question14_answer),
        question15_answer = VALUES(question15_answer),
        updated_at = NOW()
";

$stmt = $conn->prepare($query);

if (!$stmt) {
    echo json_encode(["error" => "Erreur lors de la préparation de la requête SQL : " . $conn->error]);
    exit();
}

$params = array_merge([$user_id], array_values($answers));
$types = "i" . str_repeat("s", 15);

$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    echo json_encode(["message" => "Les réponses ont été enregistrées ou mises à jour avec succès."]);

    $output = shell_exec('python3 ../../recommendations.py');
    echo $output;
} else {
    echo json_encode(["error" => "Erreur SQL : " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
