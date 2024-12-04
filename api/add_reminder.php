<?php
include '../db.php'; // Connexion à la base de données
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté.']);
    exit;
}

// Récupère les données de la requête
$input = json_decode(file_get_contents('php://input'), true);
$event_id = $input['event_id'];
$user_id = $_SESSION['user_id'];

// Vérifie si l'événement existe
$query = $pdo->prepare("SELECT date FROM events WHERE id = :event_id");
$query->execute(['event_id' => $event_id]);
$event = $query->fetch();

if (!$event) {
    echo json_encode(['success' => false, 'message' => 'Événement introuvable.']);
    exit;
}

// Calcule la date du rappel (par exemple 3 jours avant l'événement)
$reminder_date = date('Y-m-d H:i:s', strtotime($event['date'] . ' -3 days'));

// Insère le rappel dans la table
$insert = $pdo->prepare("
    INSERT INTO event_reminders (user_id, event_id, reminder_date)
    VALUES (:user_id, :event_id, :reminder_date)
");
$insert->execute([
    'user_id' => $user_id,
    'event_id' => $event_id,
    'reminder_date' => $reminder_date
]);

echo json_encode(['success' => true, 'message' => 'Rappel ajouté avec succès.']);
