<?php
/*
    Script backend qui permet de récupérer les questions du quiz depuis la bdd.

    Description :
    - Envoie les données JSON contenant les questions, types et options disponibles.
    - Transforme les chaînes d'options en tableaux pour usage côté client.

    Utilisation :
    - Requis via AJAX dans quiz-loader.js pour alimenter le quiz.

    Ressources utilisées :
    - db.php (connexion a la bdd)
    - Table : quiz_questions (id, question, type, options)
*/
session_start();
header('Content-Type: application/json');

require_once '../../db.php';

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
