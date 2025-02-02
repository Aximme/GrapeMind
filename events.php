<?php
session_start();

require_once __DIR__ . '/api/api_requests.php';
require_once __DIR__ . '/components/header.php';

$data = getWineEvents();

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$results_per_page = 8;
$total_results = count($data['objects'][0]['items']);
$offset = ($page - 1) * $results_per_page;
$events_paginated = array_slice($data['objects'][0]['items'], $offset, $results_per_page);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Événements Viticoles en France</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/search_results.css">
    <link rel="stylesheet" href="css/events.css?v=1.0">
</head>
<body>
<h1>Événements Viticoles en France
    <a href="calendrier.php" class="calendar-link"> 📅 Mon Calendrier</a>
</h1>

<div class="events-list">
    <?php if ($events_paginated): ?>
        <?php foreach ($events_paginated as $event): ?>
            <div class="event-item">
                <h2><?= htmlspecialchars($event['title'] ?? 'Titre indisponible') ?></h2>
                <p>
                    <?= htmlspecialchars($event['summary'] ?? 'Aucune description disponible.') ?>
                </p>
                <?php if (!empty($event['image'])): ?>
                    <img src="<?= htmlspecialchars($event['image']) ?>"
                         alt="<?= htmlspecialchars($event['title'] ?? 'Image') ?>"
                         class="event-image">
                <?php endif; ?>
                <div class="button-container">
                    <a href="<?= htmlspecialchars($event['link'] ?? '#') ?>" target="_blank">En savoir plus</a>
                    <form action="set_reminder.php" method="POST">
                        <input type="hidden" name="event_data" value="<?= htmlspecialchars(json_encode(['title' => $event['title'] ?? 'Titre indisponible', 'summary' => $event['summary'] ?? 'Aucune description'], JSON_UNESCAPED_UNICODE)) ?>">
                        <button type="submit"> 🔔Mettre un rappel</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Aucun événement trouvé pour cette page.</p>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/components/pagination.php'; ?>
<?php include __DIR__ . '/components/footer.php';
$_SESSION['events'] = $data['objects'][0]['items'];
?>
</body>
</html>
