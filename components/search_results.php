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
    $sql = "SELECT name, thumb, idwine FROM scrap WHERE name LIKE '%$query%' LIMIT $start, $results_per_page";
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
</head>
<body>
<h1>Résultats pour "<?php echo htmlspecialchars($query); ?>"</h1>

<div class="search-results">
    <?php if (!empty($results)): ?>
        <?php foreach ($results as $wine): ?>
            <div class="result-item">
                <img src="<?php echo htmlspecialchars($wine['thumb']); ?>" alt="<?php echo htmlspecialchars($wine['name']); ?>" class="wine-thumbnail">
                <h2><?php echo htmlspecialchars($wine['name']); ?></h2>
                <a href="/GrapeMind/components/wine/wine-details.php?idwine=<?php echo $wine['idwine']; ?>" class="details-link">Voir les détails</a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Aucun résultat trouvé pour "<?php echo htmlspecialchars($query); ?>"</p>
    <?php endif; ?>
</div>

<?php include 'pagination.php'; ?>
</body>
</html>
