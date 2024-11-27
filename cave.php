<?php
session_start();
global $conn;

include __DIR__ . '/components/header.php';
include __DIR__ . '/db.php';

if (!isset($_SESSION['user']['id'])) {
    echo "<p>Veuillez vous connecter pour voir votre cave.</p>";
    exit;
}

$id_user = $_SESSION['user']['id'];


$results_per_page = 4;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$start = ($page - 1) * $results_per_page;


$total_results_query = $conn->prepare("
    SELECT COUNT(*) as total
    FROM cave
    JOIN descriptifs ON cave.idwine = descriptifs.idwine
    JOIN scrap ON scrap.idwine = descriptifs.idwine
    WHERE cave.id_user = ?
");
if (!$total_results_query) {
    echo "<p>Erreur dans la préparation de la requête pour le comptage : " . $conn->error . "</p>";
    exit;
}

$total_results_query->bind_param("i", $id_user);
$total_results_query->execute();
$total_results_result = $total_results_query->get_result();
$total_results = $total_results_result->fetch_assoc()['total'];
$total_results_query->close();


$query = $conn->prepare("
    SELECT descriptifs.idwine, scrap.thumb, scrap.name, scrap.price, scrap.flavorGroup_1, scrap.flavorGroup_2, scrap.flavorGroup_3, scrap.average_rating 
    FROM cave 
    JOIN descriptifs ON cave.idwine = descriptifs.idwine 
    JOIN scrap ON scrap.idwine = descriptifs.idwine
    WHERE cave.id_user = ?
    LIMIT ? OFFSET ?
");
if (!$query) {
    echo "<p>Erreur dans la préparation de la requête : " . $conn->error . "</p>";
    exit;
}

$query->bind_param("iii", $id_user, $results_per_page, $start);
$query->execute();
$result = $query->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ma cave</title>
    <link rel="stylesheet" href="css/search_results.css">
    <link rel="stylesheet" href="css/cave.css">
</head>
<body>
<div class="search-results-container">
    <h1>Ma cave</h1>

    <div class="search-results">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="result-item">
                    <a href="components/wine/wine-details.php?vin_id=<?php echo $row['idwine']; ?>" class="result-item-link">
                        <img src="<?php echo htmlspecialchars($row['thumb']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" class="wine-thumbnail">
                        <div class="wine-details">
                            <h2><?php echo htmlspecialchars($row['name']); ?></h2>
                            <p class="wine-price">Prix : <?php echo htmlspecialchars(number_format($row['price'], 2)); ?> €</p>
                            <p class="wine-taste">Goûts :
                                <?php echo ($row['flavorGroup_1']); ?>,
                                <?php echo ($row['flavorGroup_2']); ?>,
                                <?php echo ($row['flavorGroup_3']); ?>
                            </p>
                            <div class="stars_notation">
                                Note :
                                <?php
                                $rating = isset($row['average_rating']) ? floatval($row['average_rating']) : 0;

                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= floor($rating)) {
                                        echo '<img src="assets/images/StarFilled.png" alt="filled star" class="star">';
                                    } elseif ($i - 0.5 <= $rating) {
                                        echo '<img src="assets/images/StarHalfFilled.png" alt="half-filled star" class="star">';
                                    } else {
                                        echo '<img src="assets/images/StarOutlineFilled.png" alt="empty star" class="star">';
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </a>
                    <div class="delete-container">
                        <form method="POST" action="remove_wine.php" class="delete-form">
                            <input type="hidden" name="idwine" value="<?php echo htmlspecialchars($row['idwine']); ?>">
                            <input type="hidden" name="context" value="cave">
                            <button type="submit" style="background: none; border: none; padding: 0; position: relative;">
                                <img src="assets/images/cross.png" alt="Supprimer" class="delete-icon">
                                <span class="tooltip">Supprimer le vin de la cave</span>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>

            <?php include __DIR__ . '/components/pagination.php'; ?>

        <?php else: ?>
            <p>Aucun vin dans votre cave pour le moment.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>