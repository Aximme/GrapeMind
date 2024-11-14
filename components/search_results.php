<?php
include __DIR__ . '/../db.php';
include '../components/header.php';
global $conn;


$query = isset($_GET['query']) ? $_GET['query'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$minRating = isset($_GET['minRating']) ? (int)$_GET['minRating'] : 0; // Notation minimum
$minPrice = isset($_GET['minPrice']) && $_GET['minPrice'] !== '' ? (float)$_GET['minPrice'] : 0;
$maxPrice = isset($_GET['maxPrice']) && $_GET['maxPrice'] !== '' ? (float)$_GET['maxPrice'] : PHP_INT_MAX;
$wineColors = isset($_GET['wineColor']) ? $_GET['wineColor'] : []; // Liste des couleurs de vin sélectionnées
$results_per_page = 10;
$start = ($page - 1) * $results_per_page;

$results = [];
$total_results = 0;

if (!empty($query)) {
    // Construire la clause WHERE pour les couleurs de vin sélectionnées
    $colorCondition = "";
    if (!empty($wineColors)) {
        $placeholders = implode(',', array_fill(0, count($wineColors), '?'));
        $colorCondition = " AND descriptifs.Type IN ($placeholders)";
    }

    // Récupérer le nombre total de résultats pour calculer le nombre de pages
    $count_sql = "SELECT COUNT(*) as total 
                  FROM scrap 
                  INNER JOIN descriptifs ON scrap.idwine = descriptifs.idwine 
                  WHERE scrap.name LIKE ? 
                  AND scrap.average_rating >= ? 
                  AND scrap.price >= ? AND scrap.price <= ?"
        . $colorCondition;

    $count_stmt = $conn->prepare($count_sql);

    // Lier les paramètres à la requête
    $query_param = "%$query%";
    $params = [$query_param, $minRating, $minPrice, $maxPrice];
    if (!empty($wineColors)) {
        foreach ($wineColors as $color) {
            $params[] = $color;
        }
    }

    $count_stmt->bind_param(str_repeat('s', count($params)), ...$params);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $total_results = $count_result->fetch_assoc()['total'];

    // Requête pour obtenir les résultats de la page actuelle
    $sql = "SELECT scrap.name, scrap.thumb, scrap.idwine, scrap.price, 
                   scrap.flavorGroup_1, scrap.flavorGroup_2, scrap.flavorGroup_3, 
                   scrap.average_rating, descriptifs.Type 
            FROM scrap 
            INNER JOIN descriptifs ON scrap.idwine = descriptifs.idwine 
            WHERE scrap.name LIKE ? 
            AND scrap.average_rating >= ? 
            AND scrap.price >= ? AND scrap.price <= ?"
        . $colorCondition . " 
            LIMIT ?, ?";
    $stmt = $conn->prepare($sql);

    $params = [$query_param, $minRating, $minPrice, $maxPrice];
    if (!empty($wineColors)) {
        foreach ($wineColors as $color) {
            $params[] = $color;
        }
    }
    $params[] = $start;
    $params[] = $results_per_page;

    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $results[] = $row;
        }
    }
}

$total_pages = ceil($total_results / $results_per_page);
$conn->close();
?>







<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Résultats de recherche pour "<?php echo htmlspecialchars($query); ?>"</title>
    <link rel="stylesheet" href="/GrapeMind/css/search_results.css">
    <!--LOADER-->
    <script defer src="/GrapeMind/js/loader.js"></script>

</head>
<body>
<h1>Résultats pour "<?php echo htmlspecialchars($query); ?>"</h1>


<div class="search-container">
    <!-- Panneau de filtre -->
    <aside class="filter-panel">
        <h2>Filtres</h2>
        <form method="get" class="filter-form">
            <input type="hidden" name="query" value="<?php echo htmlspecialchars($query); ?>">

            <!-- Filtre par prix -->
            <div class="filter-section">
                <h3>Prix</h3>
                <label for="minPrice">Prix minimum :</label>
                <input type="number" name="minPrice" id="minPrice" value="<?php echo isset($_GET['minPrice']) ? htmlspecialchars($_GET['minPrice']) : ''; ?>" min="0">
                <label for="maxPrice">Prix maximum :</label>
                <input type="number" name="maxPrice" id="maxPrice" value="<?php echo isset($_GET['maxPrice']) ? htmlspecialchars($_GET['maxPrice']) : ''; ?>" min="0">
            </div>

            <!-- Filtre par notation -->
            <div class="filter-section">
                <h3>Notation minimum :</h3>
                <div class="rating-stars">
                    <input type="radio" id="star-1" name="minRating" value="1" <?php echo $minRating == 1 ? 'checked' : ''; ?>>
                    <label for="star-1" class="star">&#9733;</label>

                    <input type="radio" id="star-2" name="minRating" value="2" <?php echo $minRating == 2 ? 'checked' : ''; ?>>
                    <label for="star-2" class="star">&#9733;</label>

                    <input type="radio" id="star-3" name="minRating" value="3" <?php echo $minRating == 3 ? 'checked' : ''; ?>>
                    <label for="star-3" class="star">&#9733;</label>

                    <input type="radio" id="star-4" name="minRating" value="4" <?php echo $minRating == 4 ? 'checked' : ''; ?>>
                    <label for="star-4" class="star">&#9733;</label>

                    <input type="radio" id="star-5" name="minRating" value="5" <?php echo $minRating == 5 ? 'checked' : ''; ?>>
                    <label for="star-5" class="star">&#9733;</label>
                </div>
            </div>

            <!-- Filtre par couleur de vin -->
            <div class="filter-section">
                <h3>Couleur du vin :</h3>
                <div class="wine-options">
                    <label>
                        <input type="checkbox" name="wineColor[]" value="Red" <?php echo in_array('Red', $wineColors) ? 'checked' : ''; ?>>
                        Rouge <span class="red-circle"></span>
                    </label>
                    <label>
                        <input type="checkbox" name="wineColor[]" value="Rosé" <?php echo in_array('Rosé', $wineColors) ? 'checked' : ''; ?>>
                        Rosé <span class="rose-circle"></span>
                    </label>
                    <label>
                        <input type="checkbox" name="wineColor[]" value="White" <?php echo in_array('White', $wineColors) ? 'checked' : ''; ?>>
                        Blanc <span class="white-circle"></span>
                    </label>
                    <label>
                        <input type="checkbox" name="wineColor[]" value="Sparkling" <?php echo in_array('Sparkling', $wineColors) ? 'checked' : ''; ?>>
                        Bulle <span class="bubble-circle"></span>
                    </label>
                </div>
            </div>

            <button type="submit" class="filter-submit">Appliquer les filtres</button>
        </form>
    </aside>




    <!-- Résultats de recherche -->
    <div class="search-results">
        <?php if (!empty($results)): ?>
            <?php foreach ($results as $wine): ?>
                <a href="javascript:void(0);" onclick="setVinId(<?php echo $wine['idwine']; ?>)" class="result-item-link">
                    <div class="result-item">
                        <img src="<?php echo htmlspecialchars($wine['thumb']); ?>" alt="<?php echo htmlspecialchars($wine['name']); ?>" class="wine-thumbnail">
                        <div class="wine-details">
                            <h2><?php echo htmlspecialchars($wine['name']); ?></h2>
                            <p class="wine-price">Prix : <?php echo htmlspecialchars($wine['price']); ?> €</p>
                            <p class="wine-taste">Goûts : <?php echo htmlspecialchars($wine['flavorGroup_1']); ?>, <?php echo htmlspecialchars($wine['flavorGroup_2']); ?>, <?php echo htmlspecialchars($wine['flavorGroup_3']); ?></p>
                            <div class="wine-flavors">
                                <!-- Icônes de goûts si présentes -->
                            </div>
                            <div class="stars_notation">
                                Note :
                                <?php
                                $rating = isset($wine['average_rating']) ? floatval($wine['average_rating']) : 0;

                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= floor($rating)) {
                                        echo '<img src="../assets/images/StarFilled.png" alt="filled star" class="star">';
                                    } elseif ($i - 0.5 <= $rating) {
                                        echo '<img src="../assets/images/StarHalfFilled.png" alt="half-filled star" class="star">';
                                    } else {
                                        echo '<img src="../assets/images/StarOutlineFilled.png" alt="empty star" class="star">';
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucun résultat trouvé pour "<?php echo htmlspecialchars($query); ?>"</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'pagination.php'; ?>
</body>
</html>

<script>
    function setVinId(vinId) {
        fetch("/GrapeMind/components/wine/set_vin_id.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: vin_id=${vinId}
    })
    .then(response => response.text())
            .then(data => {
                console.log(data); // Affiche un message de confirmation
                window.location.href = "/GrapeMind/components/wine/wine-details.php"; // Redirige vers la page de détails
            })
            .catch(error => console.error("Erreur lors de l'envoi de l'ID :", error));
    }
</script>
<link rel="stylesheet" href="/GrapeMind/css/flavor_icons.css">

