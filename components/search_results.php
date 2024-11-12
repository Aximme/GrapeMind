<?php
include __DIR__ . '/../db.php';
include '../components/header.php';
global $conn;

// Récupérer les paramètres de recherche, de page et de notation
$query = isset($_GET['query']) ? $_GET['query'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$minRating = isset($_GET['minRating']) ? (int)$_GET['minRating'] : 0; // Notation minimum
$results_per_page = 10;
$start = ($page - 1) * $results_per_page;

$results = [];
$total_results = 0;

if (!empty($query)) {
    // Récupérer le nombre total de résultats pour calculer le nombre de pages
    $count_sql = "SELECT COUNT(*) as total FROM scrap WHERE name LIKE ? AND average_rating >= ?";
    $count_stmt = $conn->prepare($count_sql);
    $query_param = "%$query%";
    $count_stmt->bind_param("si", $query_param, $minRating);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $total_results = $count_result->fetch_assoc()['total'];

    // Requête pour obtenir les résultats de la page actuelle
    $sql = "SELECT name, thumb, idwine, price, flavorGroup_1, flavorGroup_2, flavorGroup_3, average_rating 
            FROM scrap 
            WHERE name LIKE ? AND average_rating >= ? 
            LIMIT ?, ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siii", $query_param, $minRating, $start, $results_per_page);
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

<!-- Formulaire de tri par notation -->
<form method="get" class="sort-form">
    <label for="minRating">Notation minimum :</label>
    <select name="minRating" id="minRating" onchange="this.form.submit()">
        <option value="0" <?php echo $minRating == 0 ? 'selected' : ''; ?>>Aucune</option>
        <option value="1" <?php echo $minRating == 1 ? 'selected' : ''; ?>>1 étoile</option>
        <option value="2" <?php echo $minRating == 2 ? 'selected' : ''; ?>>2 étoiles</option>
        <option value="3" <?php echo $minRating == 3 ? 'selected' : ''; ?>>3 étoiles</option>
        <option value="4" <?php echo $minRating == 4 ? 'selected' : ''; ?>>4 étoiles</option>
        <option value="5" <?php echo $minRating == 5 ? 'selected' : ''; ?>>5 étoiles</option>
    </select>
    <input type="hidden" name="query" value="<?php echo htmlspecialchars($query); ?>">
</form>

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
                console.log(data); // Affiche un message de confirmation
                window.location.href = "/GrapeMind/components/wine/wine-details.php"; // Redirige vers la page de détails
            })
            .catch(error => console.error("Erreur lors de l'envoi de l'ID :", error));
    }
</script>
<link rel="stylesheet" href="/GrapeMind/css/flavor_icons.css">
