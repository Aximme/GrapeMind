<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../db.php';

if (!isset($conn)) {
    echo json_encode(["error" => "Erreur de connexion à la base de données."]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $conn->prepare("SELECT id, question, type, options FROM quiz_questions ORDER BY id ASC");

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $questions = [];

        while ($row = $result->fetch_assoc()) {
            if (!empty($row['options']) && is_string($row['options'])) {
                $row['options'] = explode(',', $row['options']);
            } else {
                $row['options'] = [];
            }

            $row['input'] = ($row['type'] === 'input');
            $questions[] = $row;
        }

        echo json_encode($questions);
    } else {
        echo json_encode(["error" => "Erreur lors de la récupération des questions : " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
    exit();
}
?>
