<?php
include __DIR__ . '/../db.php';
include '../components/header.php';
global $conn;

// Récupérer le terme de recherche et la page actuelle depuis l'URL
$query = isset($_GET['query']) ? $_GET['query'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$results_per_page = 10;
$start = ($page - 1) * $results_per_page;

$results = [];
$total_results = 0;

if (!empty($query)) {
    // Récupérer le nombre total de résultats pour calculer le nombre de pages
    $count_sql = "SELECT COUNT(*) as total FROM scrap WHERE name LIKE '%$query%'";
    $count_result = $conn->query($count_sql);
    $total_results = $count_result->fetch_assoc()['total'];

    // Requête pour obtenir les résultats de la page actuelle
    $sql = "SELECT name, thumb, idwine, price, flavorGroup_1, flavorGroup_2, flavorGroup_3 FROM scrap WHERE name LIKE '%$query%' LIMIT $start, $results_per_page";
    $result = $conn->query($sql);

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
<body>
<h1>Résultats pour "<?php echo htmlspecialchars($query); ?>"</h1>

<div class="search-results">
    <?php if (!empty($results)): ?>
        <?php foreach ($results as $wine): ?>
            <div class="result-item">
                <img src="<?php echo htmlspecialchars($wine['thumb']); ?>" alt="<?php echo htmlspecialchars($wine['name']); ?>" class="wine-thumbnail">
                <div class="wine-details">
                    <h2><?php echo htmlspecialchars($wine['name']); ?></h2>
                    <p class="wine-price">Prix : <?php echo htmlspecialchars($wine['price']); ?> €</p>
                    <p class="wine-taste">Goûts : <?php echo htmlspecialchars($wine['flavorGroup_1']); ?>, <?php echo htmlspecialchars($wine['flavorGroup_2']); ?>, <?php echo htmlspecialchars($wine['flavorGroup_3']); ?></p>
                    <div class="wine-flavors">
                        <?php
                        // Flavor Group 1
                        if (!empty($wine['flavorGroup_1'])) {
                            $flavor = trim($wine['flavorGroup_1']);
                            $flavor = str_replace(["'", "-", "’", " "], "_", strtolower($flavor));
                            $flavorImagePath = '/GrapeMind/assets/gouts/' . strtolower(str_replace(' ', '_', $flavor)) . '.jpeg';
                            echo '<img class="icon-flavor" src="' . htmlspecialchars($flavorImagePath) . '" alt="' . htmlspecialchars($flavor) . '"> ';
                        }

                        // Flavor Group 2
                        if (!empty($wine['flavorGroup_2'])) {
                            $flavor = trim($wine['flavorGroup_2']);
                            $flavor = str_replace(["'", "-", "’", " "], "_", strtolower($flavor));
                            $flavorImagePath = '/GrapeMind/assets/gouts/' . strtolower(str_replace(' ', '_', $flavor)) . '.jpeg';
                            echo '<img class="icon-flavor" src="' . htmlspecialchars($flavorImagePath) . '" alt="' . htmlspecialchars($flavor) . '"> ';
                        }

                        // Flavor Group 3
                        if (!empty($wine['flavorGroup_3'])) {
                            $flavor = trim($wine['flavorGroup_3']);
                            $flavor = str_replace(["'", "-", "’", " "], "_", strtolower($flavor));
                            $flavorImagePath = '/GrapeMind/assets/gouts/' . strtolower(str_replace(' ', '_', $flavor)) . '.jpeg';
                            echo '<img class="icon-flavor" src="' . htmlspecialchars($flavorImagePath) . '" alt="' . htmlspecialchars($flavor) . '"> ';
                        }
                        ?>
                    </div>
                    <a href="javascript:void(0);" onclick="setVinId(<?php echo $wine['idwine']; ?>)" class="details-link">Voir les détails</a>
                </div>
            </div>
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
