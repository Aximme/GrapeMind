<?php
session_start();
require_once('db.php');
include 'components/header.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['user']['username'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil</title>
    <link rel="stylesheet" href="/GrapeMind/css/profile_user.css">

</head>
<body>
<div class="profile-container">
    <div class="welcome-section">
        <h3>Bienvenue, <?php echo htmlspecialchars($username); ?></h3>
    </div>
    <div class="content-section">
        <div class="recent-searches">
            <h4>Recherche récente</h4>
        </div>
        <div class="quick-access">
            <h4>Accès Rapide</h4>
            <div class="access-item">
                <a href="ma_cave.php">
                    <div class="icon-container">
                        <img src="assets/images/cave-logo.png" alt="Icone Cave">
                    </div>
                    <p>Ma Cave</p>
                </a>
            </div>
            <div class="access-item">
                <a href="mon_grenier.php">
                    <div class="icon-container">
                        <img src="assets/images/winecavestock-logo.png" alt="Icone Grenier">
                    </div>
                    <p>Mon Grenier</p>
                </a>
            </div>
        </div>
    </div>
    <div class="recommendation-button">
        <a href="/GrapeMind/components/quizz/index.php">
            <button>Affiner mes recommandations</button>
        </a>
    </div>


</div>
</body>
</html>
