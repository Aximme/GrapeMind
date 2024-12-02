<?php
session_start();
global $conn;

include __DIR__ . '/components/header.php';
include __DIR__ . '/db.php';

if (!isset($_SESSION['user']['id'])) {
    echo "<p>Veuillez vous connecter pour voir votre grenier.</p>";
    exit;
}

$id_user = $_SESSION['user']['id'];

$query = $conn->prepare("
    SELECT descriptifs.idwine, scrap.thumb, scrap.name, scrap.price, scrap.flavorGroup_1, scrap.flavorGroup_2, scrap.flavorGroup_3, scrap.average_rating 
    FROM grenier 
    JOIN descriptifs ON grenier.idwine = descriptifs.idwine 
    JOIN scrap ON scrap.idwine = descriptifs.idwine
    WHERE grenier.id_user = ?
");
if (!$query) {
    echo "<p>Erreur dans la préparation de la requête : " . $conn->error . "</p>";
    exit;
}

$query->bind_param("i", $id_user);
$query->execute();
$result = $query->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Grenier</title>
    <link rel="stylesheet" href="css/search_results.css">
    <link rel="stylesheet" href="css/grenier.css">
</head>
<body>
<div class="search-results-container">
    <h1>Mon Grenier</h1>

    <?php if (isset($_GET['success'])): ?>
        <p class="success-message">Le vin a été supprimé avec succès !</p>
    <?php endif; ?>

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
        <?php else: ?>
            <p>Aucun vin dans votre grenier pour le moment.</p>
        <?php endif; ?>
    </div>
</div>
<?php include __DIR__ . '/components/footer.php'; ?>

</body>
</html>
