<?php
// Activer les erreurs pour le débogage
session_start();

// Inclure les fichiers nécessaires avec require_once pour éviter les inclusions multiples

require_once __DIR__ . '/api/api_requests.php';
require_once __DIR__ . '/components/header.php';

// Récupérer les données des événements viticoles via l'API Diffbot
$data = getWineEvents();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Événements Viticoles en France</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/events.css?v=1.0">
</head>
<body>
<h1>Événements Viticoles en France
<a href="calendrier.php" class="calendar-link"> 📅 Mon Calendrier</a>
</h1>

<?php if ($data && isset($data['objects'][0]['items'])): ?>
    <div class="events-list">
        <?php foreach ($data['objects'][0]['items'] as $event): ?>
            <div class="event-item">
                <h2><?= htmlspecialchars($event['title'] ?? 'Titre indisponible') ?></h2>
                <p>
                    <strong>Description :</strong>
                    <?= htmlspecialchars($event['summary'] ?? 'Aucune description disponible.') ?>
                </p>
                <?php if (!empty($event['image'])): ?>
                    <img src="<?= htmlspecialchars($event['image']) ?>"
                         alt="<?= htmlspecialchars($event['title'] ?? 'Image') ?>"
                         class="event-image">
                <?php endif; ?>
                <p class="button-container">

                    <a href="<?= htmlspecialchars($event['link'] ?? '#') ?>" target="_blank">En savoir plus</a>
                    <?php
                        $title = $event['title'] ?? 'Titre indisponible';
                        $summary = $event['summary'] ?? 'Description indisponible';
                    ?>
                <p class="button-container">
                    <a href="set_reminder.php?event_data=<?= urlencode(json_encode(['title' => $title, 'summary' => $summary])) ?>">Se souvenir de cet événement</a>
                </p>



                </p>
                
            </div>

            
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>Aucun événement trouvé pour le moment.</p>
    <?php if ($data): ?>
        <!-- Débogage : Afficher les données si l'API retourne une réponse inattendue -->
        <pre><?php print_r($data); ?></pre>
    <?php endif; ?>
<?php endif; ?>

<?php include __DIR__ . '/components/footer.php'; 
// Stocker les données de l'API dans une session
$_SESSION['events'] = $data['objects'][0]['items'];
?>
</body>
</html>


