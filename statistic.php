<?php
global $conn;
session_start();
require_once('db.php');
include 'components/header.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];

// nb vins consultés
$query_vins_consultes = "SELECT COUNT(*) AS total_vins_consultes FROM vins_consultes WHERE user_id = ?";
$stmt_vins_consultes = $conn->prepare($query_vins_consultes);
$stmt_vins_consultes->bind_param("i", $user_id);
$stmt_vins_consultes->execute();
$result_vins_consultes = $stmt_vins_consultes->get_result();
$row_vins_consultes = $result_vins_consultes->fetch_assoc();
$total_vins_consultes = $row_vins_consultes['total_vins_consultes'];

// nb vins dans la cave
$query_cave = "SELECT COUNT(*) AS total_vins_cave FROM cave WHERE user_id = ?";
$stmt_cave = $conn->prepare($query_cave);
$stmt_cave->bind_param("i", $user_id);
$stmt_cave->execute();
$result_cave = $stmt_cave->get_result();
$row_cave = $result_cave->fetch_assoc();
$total_vins_cave = $row_cave['total_vins_cave'];

// nb vins au grenier
$query_grenier = "SELECT COUNT(*) AS total_vins_grenier FROM grenier WHERE user_id = ?";
$stmt_grenier = $conn->prepare($query_grenier);
$stmt_grenier->bind_param("i", $user_id);
$stmt_grenier->execute();
$result_grenier = $stmt_grenier->get_result();
$row_grenier = $result_grenier->fetch_assoc();
$total_vins_grenier = $row_grenier['total_vins_grenier'];

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vos Statistiques</title>
    <link rel="stylesheet" href="/GrapeMind/css/statistic.css">
</head>
<body>
<div class="stats-container">
    <h2>Vos Statistiques</h2>
    <div class="stats-box">
        <div class="stat-item">
            <img src="/GrapeMind/assets/images/default-pp.png" alt="Vins consultés">
            <p><strong>Vins consultés:</strong> <?php echo $total_vins_consultes; ?></p>
        </div>
        <div class="stat-item">
            <img src="/GrapeMind/assets/images/cave-logo.png" alt="Vins à la cave">
            <p><strong>Vins à la cave:</strong> <?php echo $total_vins_cave; ?></p>
        </div>
        <div class="stat-item">
            <img src="/GrapeMind/assets/images/winecavestock-logo.png" alt="Vins au grenier">
            <p><strong>Vins au Grenier:</strong> <?php echo $total_vins_grenier; ?></p>
        </div>
    </div>
    <div class="preferences">
        <h2>Vos Préférences</h2>
        <canvas id="preferencesChart"></canvas>
    </div>
    <div class="last-connexion">
        <p><strong>Dernière
                Connexion:</strong> <?php echo date('d/m/Y à H:i', strtotime($_SESSION['user']['last_login'])); ?></p>
    </div>
</div>
</body>
</html>
