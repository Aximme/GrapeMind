<?php
require_once __DIR__ . '/db.php';

if (!isset($conn)) {
    die(json_encode(["error" => "Erreur de connexion à la base de données."]));
}

session_start();
if (!isset($_SESSION['user']['id'])) {
    die(json_encode(["error" => "Utilisateur non connecté."]));
}

$user_id = $_SESSION['user']['id'];



$query = "SELECT * FROM quiz_answers WHERE user_id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die(json_encode(["error" => "Erreur lors de la préparation de la requête."]));
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_answers = $stmt->get_result()->fetch_assoc();

if (!$user_answers) {
    die(json_encode(["error" => "Aucune réponse trouvée pour l'utilisateur."]));
}


$selected_flavors = extract_selected_flavors($user_answers);


$query = "
    SELECT DISTINCT wr.wine_id, wr.name, wr.price, wr.thumb
    FROM wine_recommendations wr
    WHERE wr.user_id = ?
    ORDER BY wr.price ASC
";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die(json_encode(["error" => "Erreur lors de la préparation de la requête SQL."]));
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die(json_encode(["error" => "Erreur lors de l'exécution de la requête SQL."]));
}


$wines = $result->fetch_all(MYSQLI_ASSOC);


if (empty($wines)) {
    echo json_encode([]);
    exit;
}


foreach ($wines as &$wine) {
    $wine["selected_flavors"] = $selected_flavors;

}


header("Content-Type: application/json");
echo json_encode($wines);


function extract_selected_flavors($user_answers) {
    $selected_flavors = [];
    $question_to_flavor = [
        "question2_answer" => "fruit rouge",
        "question3_answer" => "fruit d'arbre fruitier",
        "question4_answer" => "fruit noir",
        "question5_answer" => "Vieillissement",
        "question6_answer" => "boisé",
        "question7_answer" => "Terreux",
        "question8_answer" => "agrume",
        "question9_answer" => "tropical",
        "question10_answer" => "épices",
        "question11_answer" => "fruit sec",
        "question12_answer" => "levure",
        "question13_answer" => "végétal",
        "question14_answer" => "Floral"
    ];

    foreach ($question_to_flavor as $question => $flavor) {
        if (!empty($user_answers[$question]) && strpos($user_answers[$question], $flavor) !== false) {
            $selected_flavors[] = $flavor;
        }
    }

    return $selected_flavors;
}
?>
