<?php
require_once 'db.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = intval($_POST['event_id']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

    if ($email && $event_id) {
        $stmt = $conn->prepare("INSERT INTO event_registrations (event_id, email) VALUES (?, ?)");
        $stmt->bind_param("is", $event_id, $email);
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Vous êtes inscrit au rappel pour cet événement !"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Erreur lors de l'inscription."]);
        }
        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Email ou ID d'événement invalide."]);
    }
}
?>
