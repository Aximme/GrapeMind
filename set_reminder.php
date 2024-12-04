<?php
session_start();
include 'db.php';
require_once __DIR__ . '/api/api_requests.php';
require_once __DIR__ . '/components/header.php';

// Vérifie si la connexion à la base de données est établie
if (!$conn) {
    die("La connexion à la base de données n'est pas établie. Vérifiez `db.php`.");
}

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user']['id'])) {
    echo "Vous devez être connecté pour définir un rappel.";
    exit;
}

// Récupère les données de l'événement depuis GET ou POST
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $event_data = $_GET['event_data'] ?? null;
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_data = $_POST['event_data'] ?? null;
}

if (!$event_data) {
    echo "Aucun événement sélectionné.";
    exit;
}

// Décoder les données JSON
$event = json_decode($event_data, true);
if (!$event || !isset($event['title'])) {
    echo "Données d'événement invalides.";
    exit;
}

// Traitement de la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reminder_date = $_POST['reminder_date'] ?? null;

    if ($reminder_date) {
        $user_id = $_SESSION['user']['id'];
        $event_title = $event['title'];

        // Vérifiez si l'événement existe dans `events`
        $stmt = $conn->prepare("SELECT id FROM events WHERE name = ?");
        $stmt->bind_param('s', $event_title);
        $stmt->execute();
        $result = $stmt->get_result();
        $existing_event = $result->fetch_assoc();

        if (!$existing_event) {
            try {
                $stmt = $conn->prepare("INSERT INTO events (name) VALUES (?)");
                $stmt->bind_param('s', $event_title);
                $stmt->execute();
                $event_id = $conn->insert_id;
            } catch (mysqli_sql_exception $e) {
                if ($e->getCode() === 1062) {
                    $stmt = $conn->prepare("SELECT id FROM events WHERE name = ?");
                    $stmt->bind_param('s', $event_title);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $existing_event = $result->fetch_assoc();
                    $event_id = $existing_event['id'];
                } else {
                    throw $e;
                }
            }
        } else {
            $event_id = $existing_event['id'];
        }

        // Vérifiez si un rappel existe déjà
        $stmt = $conn->prepare("
            SELECT id FROM event_reminders 
            WHERE user_id = ? AND event_id = ? AND reminder_date = ?
        ");
        $stmt->bind_param('iis', $user_id, $event_id, $reminder_date);
        $stmt->execute();
        $existing_reminder = $stmt->get_result()->fetch_assoc();

        if (!$existing_reminder) {
            $stmt = $conn->prepare("
                INSERT INTO event_reminders (user_id, event_id, event_title, reminder_date) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->bind_param('iiss', $user_id, $event_id, $event_title, $reminder_date);
            $stmt->execute();

            echo "<p>Rappel enregistré avec succès pour l'événement : " . htmlspecialchars($event_title) . " à la date : " . htmlspecialchars($reminder_date) . "</p>";
        } else {
            echo "<p>Un rappel existe déjà pour cet événement à la date sélectionnée.</p>";
        }
    } else {
        echo "<p>Veuillez sélectionner une date de rappel.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="css/add_event.css?v=1.0">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/checkAdult.css">
    <link rel="stylesheet" href="css/filter-wine-index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.6.1/nouislider.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.6.1/nouislider.min.js"></script>


</head>
<body>
    <div class="page-container">
        <div class="event-details">
            <h1 class="event-title">Événement sélectionné : <?= htmlspecialchars($event['title']) ?></h1>
            <p class="event-description"><?= htmlspecialchars($event['summary'] ?? 'Pas de description disponible.') ?></p>
        </div>

        <!-- Formulaire pour définir le rappel -->
        <div class="form-container">
            <form action="set_reminder.php" method="POST">
                <!-- Champ caché pour transmettre les données de l'événement -->
                <input type="hidden" name="event_data" value="<?= htmlspecialchars(json_encode($event)) ?>">

                <label for="reminder_date">Date du rappel :</label>
                <input type="date" id="reminder_date" name="reminder_date" required>

                <button type="submit">Enregistrer le rappel</button>
            </form>
        </div>
<?php include __DIR__ . '/components/footer.php'; ?>

</html>
