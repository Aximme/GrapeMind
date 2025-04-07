<?php
session_start();
require_once __DIR__ . ('/db.php');
include __DIR__ . '/components/header.php';

global $conn;
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];

// Mettre à jour la date de dernière connexion
$update_last_login = "UPDATE users SET last_login = NOW() WHERE id = ?";
$stmt_update_login = $conn->prepare($update_last_login);
$stmt_update_login->bind_param("i", $user_id);
$stmt_update_login->execute();

// Récupérer la date de dernière connexion
$query_last_login = "SELECT last_login FROM users WHERE id = ?";
$stmt_last_login = $conn->prepare($query_last_login);
$stmt_last_login->bind_param("i", $user_id);
$stmt_last_login->execute();
$result_last_login = $stmt_last_login->get_result();
$last_login = $result_last_login->fetch_assoc()['last_login'];

// Récupérer le nombre de vins dans la cave
$query_cave_count = "SELECT COUNT(*) as total_cave FROM cave WHERE id_user = ?";
$stmt_cave_count = $conn->prepare($query_cave_count);
$stmt_cave_count->bind_param("i", $user_id);
$stmt_cave_count->execute();
$result_cave_count = $stmt_cave_count->get_result();
$total_cave = $result_cave_count->fetch_assoc()['total_cave'];

// Récupérer le nombre de vins dans le grenier
$query_grenier_count = "SELECT COUNT(*) as total_grenier FROM grenier WHERE id_user = ?";
$stmt_grenier_count = $conn->prepare($query_grenier_count);
$stmt_grenier_count->bind_param("i", $user_id);
$stmt_grenier_count->execute();
$result_grenier_count = $stmt_grenier_count->get_result();
$total_grenier = $result_grenier_count->fetch_assoc()['total_grenier'];

// Récupérer le nombre de vins par type
$query_vins_types = "
    SELECT d.Type, COUNT(*) as total 
    FROM (
        SELECT c.idwine FROM cave c WHERE c.id_user = ?
        UNION ALL
        SELECT g.idwine FROM grenier g WHERE g.id_user = ?
    ) as vins
    JOIN descriptifs d ON vins.idwine = d.idwine
    GROUP BY d.Type";

$stmt_vins_types = $conn->prepare($query_vins_types);
$stmt_vins_types->bind_param("ii", $user_id, $user_id);
$stmt_vins_types->execute();
$result_vins_types = $stmt_vins_types->get_result();

$wine_preferences = ["Rouge" => 0, "Blanc" => 0, "Rosé" => 0, "Pétillants" => 0];

$type_mapping = [
    "Red" => "Rouge",
    "White" => "Blanc",
    "Rosé" => "Rosé",
    "Sparkling" => "Pétillants",
    "Dessert" => "Pétillants"
];

while ($row = $result_vins_types->fetch_assoc()) {
    $type = $row["Type"];

    if (isset($type_mapping[$type])) {
        $type = $type_mapping[$type];
    }
    
    if (isset($wine_preferences[$type])) {
        $wine_preferences[$type] += (int) $row["total"];
    } else {
        $wine_preferences[$type] = (int) $row["total"];
    }
}


$wine_preferences_json = json_encode(array_values($wine_preferences));
$wine_labels_json = json_encode(array_keys($wine_preferences));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Vos Statistiques</title>
    <link rel="stylesheet" href="/GrapeMind/css/statistic.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="stats-container">
    <h2>Vos Statistiques</h2>
    <p>Dernière connexion : <?php echo $last_login; ?></p>
    <p>Nombre de vins dans la cave : <?php echo $total_cave; ?></p>
    <p>Nombre de vins dans le grenier : <?php echo $total_grenier; ?></p>
    <div class="chart-gallery">
        <canvas id="preferencesChart" style="width: 100%; height: 500px;"></canvas>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        var wineLabels = <?php echo $wine_labels_json; ?>;
        var winePreferences = <?php echo $wine_preferences_json; ?>;

        var ctx = document.getElementById('preferencesChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: wineLabels,
                datasets: [{
                    label: 'Nombre de vins',
                    data: winePreferences,
                    backgroundColor: ['rgba(255, 99, 132, 0.5)', 'rgba(54, 162, 235, 0.5)', 'rgba(255, 206, 86, 0.5)', 'rgba(75, 192, 192, 0.5)'],
                    borderColor: ['rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)', 'rgba(75, 192, 192, 1)'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>

</body>
</html>

