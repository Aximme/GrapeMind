<?php
session_start();
include 'db.php';
require_once __DIR__ . '/components/header.php';

// Vérifie si la connexion à la base de données est établie
if (!$conn) {
    die("La connexion à la base de données n'est pas établie. Vérifiez `db.php`.");
}

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user']['id'])) {
    echo "Vous devez être connecté pour voir vos rappels.";
    exit;
}

$user_id = $_SESSION['user']['id'];

// Récupérer les rappels de l'utilisateur
$stmt = $conn->prepare("
    SELECT er.reminder_date, er.event_title, e.name AS event_name, e.date AS event_date 
    FROM event_reminders er
    JOIN events e ON er.event_id = e.id
    WHERE er.user_id = ?
    ORDER BY er.reminder_date ASC
");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$rappels = [];
while ($row = $result->fetch_assoc()) {
    $rappels[] = $row;
}

// Récupérer les données des événements de l'API
$events = $_SESSION['events'] ?? []; // Les données doivent être stockées dans $_SESSION['events']
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Calendrier</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/calendrier.css">
</head>
<body>
    <div class="calendar-container">
        <h1>Mon Calendrier</h1>

        <?php if (empty($rappels)): ?>
            <p class="no-rappels">Vous n'avez pas encore de rappels enregistrés.</p>
        <?php else: ?>
            <?php foreach ($rappels as $rappel): ?>
                <?php
                // Trouver l'image associée dans les données de l'API
                $event = array_filter($events, function ($e) use ($rappel) {
                    return $e['title'] === $rappel['event_name'];
                });
                $event = !empty($event) ? array_values($event)[0] : null;
                ?>
                <div class="rappel-card">
                    <?php if (!empty($event['image'])): ?>
                        <img src="<?= htmlspecialchars($event['image']) ?>" 
                             alt="<?= htmlspecialchars($rappel['event_name']) ?>" 
                             class="rappel-image">
                    <?php endif; ?>
                    <div class="rappel-content">
                        <div class="rappel-title"><?= htmlspecialchars($rappel['event_name']) ?></div>
                        <div class="rappel-date">
                            🔔 Rappel prévu le : <?= htmlspecialchars($rappel['reminder_date']) ?>
                        </div>
                        <div class="rappel-details">
                            <strong>Description :</strong> <?= htmlspecialchars($rappel['event_title']) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <?php include __DIR__ . '/components/footer.php'; ?>
</body>
</html>
