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
    <link rel="stylesheet" href="css/events.css">
</head>
<body>
<h1>Événements Viticoles en France</h1>

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
                <p>
                    <a href="<?= htmlspecialchars($event['link'] ?? '#') ?>" target="_blank">En savoir plus</a>
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

<?php include __DIR__ . '/components/footer.php'; ?>
</body>
</html>


