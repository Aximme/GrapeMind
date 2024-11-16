<?php
include __DIR__ . '/../db.php';
include '../components/header.php';


global $conn;

$query = isset($_GET['query']) ? $_GET['query'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$minRating = isset($_GET['minRating']) ? (int)$_GET['minRating'] : 0; // Notation minimum
$minPrice = isset($_GET['minPrice']) && $_GET['minPrice'] !== '' ? (float)$_GET['minPrice'] : 0;
$maxPrice = isset($_GET['maxPrice']) && $_GET['maxPrice'] !== '' ? (float)$_GET['maxPrice'] : PHP_INT_MAX;
$wineColors = isset($_GET['wineColor']) ? $_GET['wineColor'] : [];

$results_per_page = 10;
$start = ($page - 1) * $results_per_page;

$results = [];
$total_results = 0;

if (!empty($query) || !empty($wineColors) || $minRating > 0 || $minPrice > 0 || $maxPrice < PHP_INT_MAX) {
    $colorCondition = "";
    if (!empty($wineColors)) {
        $placeholders = implode(',', array_fill(0, count($wineColors), '?'));
        $colorCondition = " AND descriptifs.Type IN ($placeholders)";
    }

    $count_sql = "SELECT COUNT(*) as total 
                  FROM scrap 
                  INNER JOIN descriptifs ON scrap.idwine = descriptifs.idwine 
                  WHERE 1=1";

    if (!empty($query)) {
        $count_sql .= " AND scrap.name LIKE ?";
    }

    $count_sql .= " AND scrap.average_rating >= ? 
                    AND scrap.price >= ? AND scrap.price <= ?"
        . $colorCondition;

    $count_stmt = $conn->prepare($count_sql);

    $params = [];
    if (!empty($query)) {
        $query_param = "%$query%";
        $params[] = $query_param;
    }
    $params = array_merge($params, [$minRating, $minPrice, $maxPrice]);
    if (!empty($wineColors)) {
        foreach ($wineColors as $color) {
            $params[] = $color;
        }
    }

    $count_stmt->bind_param(str_repeat('s', count($params)), ...$params);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $total_results = $count_result->fetch_assoc()['total'];

    $sql = "SELECT scrap.name, scrap.thumb, scrap.idwine, scrap.price, 
                   scrap.flavorGroup_1, scrap.flavorGroup_2, scrap.flavorGroup_3, 
                   scrap.average_rating, descriptifs.Type 
            FROM scrap 
            INNER JOIN descriptifs ON scrap.idwine = descriptifs.idwine 
            WHERE 1=1";

    if (!empty($query)) {
        $sql .= " AND scrap.name LIKE ?";
    }

    $sql .= " AND scrap.average_rating >= ? 
              AND scrap.price >= ? AND scrap.price <= ?"
        . $colorCondition . " 
            LIMIT ?, ?";
    $stmt = $conn->prepare($sql);

    $params = [];
    if (!empty($query)) {
        $params[] = $query_param;
    }
    $params = array_merge($params, [$minRating, $minPrice, $maxPrice]);
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
    <script defer src="/GrapeMind/js/loader.js"></script>
</head>
<body>
<h1>Résultats pour "<?php echo htmlspecialchars($query); ?>"</h1>

<div class="search-container">
    <?php include __DIR__ . '/../components/filter.php'; ?>

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
            body: `vin_id=${vinId}`
        })
            .then(response => response.text())
            .then(data => {
                console.log(data);
                window.location.href = "/GrapeMind/components/wine/wine-details.php";
            })
            .catch(error => console.error("Erreur lors de l'envoi de l'ID :", error));
    }
</script>
<link rel="stylesheet" href="/GrapeMind/css/flavor_icons.css">
