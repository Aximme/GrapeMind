<?php
include __DIR__ . '/../db.php';
include '../components/header.php';
global $conn;

// Récupérer les paramètres de filtrage et la page actuelle
$wineTypes = isset($_GET['wineTypes']) ? $_GET['wineTypes'] : [];
$minPrice = isset($_GET['minPrice']) ? (int)$_GET['minPrice'] : 10;
$maxPrice = isset($_GET['maxPrice']) ? (int)$_GET['maxPrice'] : 500;
$sortOrder = isset($_GET['sortOrder']) && in_array($_GET['sortOrder'], ['asc', 'desc']) ? $_GET['sortOrder'] : 'asc';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$results_per_page = 10;
$start = ($page - 1) * $results_per_page;

// Initialisation des conditions et des paramètres pour la requête
$sql_conditions = [];
$params = [];
$types = '';

// Filtrer par type de vin
if (!empty($wineTypes)) {
    $placeholders = implode(',', array_fill(0, count($wineTypes), '?'));
    $sql_conditions[] = "descriptifs.Type IN ($placeholders)";
    $params = array_merge($params, $wineTypes);
    $types .= str_repeat('s', count($wineTypes));
}

// Filtrer par prix
$sql_conditions[] = "scrap.price BETWEEN ? AND ?";
$params[] = $minPrice;
$params[] = $maxPrice;
$types .= 'ii';

// Compter le nombre total de résultats
$sql_conditions_str = implode(' AND ', $sql_conditions);
$count_sql = "SELECT COUNT(*) as total FROM scrap JOIN descriptifs ON scrap.idwine = descriptifs.idwine";
if (!empty($sql_conditions_str)) {
    $count_sql .= " WHERE " . $sql_conditions_str;
}

$count_stmt = $conn->prepare($count_sql);
$count_stmt->bind_param($types, ...$params);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_results = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_results / $results_per_page);

// Construire la requête principale avec les filtres et la limite pour la pagination
$sql = "SELECT scrap.name, scrap.thumb, scrap.idwine, scrap.price, descriptifs.Type 
        FROM scrap 
        JOIN descriptifs ON scrap.idwine = descriptifs.idwine";
if (!empty($sql_conditions_str)) {
    $sql .= " WHERE " . $sql_conditions_str;
}
$sql .= " ORDER BY scrap.price $sortOrder LIMIT ? OFFSET ?";
$params[] = $results_per_page;
$params[] = $start;
$types .= 'ii';

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();

$result = $stmt->get_result();
$results = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Résultats de recherche</title>
    <link rel="stylesheet" href="/GrapeMind/css/results_filter.css">
</head>
<body>

<h1>Résultats de recherche</h1>
<script defer src="/GrapeMind/js/loader.js"></script>

<!-- Formulaire de tri par prix -->
<form method="get" class="sort-form">
    <label for="sortOrder">Trier par prix :</label>
    <select name="sortOrder" id="sortOrder" onchange="this.form.submit()">
        <option value="asc" <?php echo $sortOrder === 'asc' ? 'selected' : ''; ?>>Croissant</option>
        <option value="desc" <?php echo $sortOrder === 'desc' ? 'selected' : ''; ?>>Décroissant</option>
    </select>
    <?php
    // Garder les autres filtres dans le formulaire
    foreach ($wineTypes as $wineType) {
        echo '<input type="hidden" name="wineTypes[]" value="' . htmlspecialchars($wineType) . '">';
    }
    echo '<input type="hidden" name="minPrice" value="' . htmlspecialchars($minPrice) . '">';
    echo '<input type="hidden" name="maxPrice" value="' . htmlspecialchars($maxPrice) . '">';
    ?>
</form>

<div class="search-results">
    <?php if (!empty($results)): ?>
        <?php foreach ($results as $wine): ?>
            <div class="result-item">
                <img src="<?php echo htmlspecialchars($wine['thumb']); ?>" alt="<?php echo htmlspecialchars($wine['name']); ?>" class="wine-thumbnail">
                <h2><?php echo htmlspecialchars($wine['name']); ?></h2>
                <p>Type : <?php echo htmlspecialchars($wine['Type']); ?></p>
                <p>Prix : <?php echo htmlspecialchars($wine['price']); ?>€</p>
                <a href="javascript:void(0);" onclick="setVinId(<?php echo $wine['idwine']; ?>)" class="details-link">Voir les détails</a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Aucun résultat trouvé pour les critères sélectionnés.</p>
    <?php endif; ?>
</div>

<!-- Inclure la pagination -->
<?php include 'pagination.php'; ?>

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

</body>
</html>
