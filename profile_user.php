<?php
global $conn;
session_start();
require_once __DIR__ . ('/db.php');
include __DIR__ . '/components/header.php';

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
    <link rel="stylesheet" href="/css/profile_user.css">




</head>
<body>

<div class="profile-container">
    <div class="welcome-section">
        <h3>Bienvenue, <?php echo htmlspecialchars($username); ?></h3>
    </div>
    <div class="content-section">
        <div class="recent-searches">
            <h4>Vins récemment consultés</h4>
            <?php
            $userId = $_SESSION['user']['id'];
            $stmt = $conn->prepare("SELECT wine_id, wine_name, consulted_at 
                            FROM recent_views 
                            WHERE user_id = ? 
                            ORDER BY consulted_at DESC 
                            LIMIT 5");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                echo '<ul>';
                while ($row = $result->fetch_assoc()) {
                    echo '<li><a href="/GrapeMind/components/wine/wine-details.php?id=' . $row['wine_id'] . '">'
                        . htmlspecialchars($row['wine_name']) . '</a> 
                 <span style="color:gray; font-size:0.8em;">(' . $row['consulted_at'] . ')</span></li>';
                }
                echo '</ul>';
            } else {
                echo '<p>Aucun vin consulté récemment.</p>';
            }
            ?>
        </div>

    <div class="quick-access">
            <h4>Accès Rapide</h4>
            <div class="access-item">
                <a href="cave.php">
                    <div class="icon-container">
                        <img src="assets/images/cave-logo.png" alt="Icone Cave">
                    </div>
                    <p>Ma Cave</p>
                </a>
            </div>
            <div class="access-item">
                <a href="grenier.php">
                    <div class="icon-container">
                        <img src="assets/images/winecavestock-logo.png" alt="Icone Grenier">
                    </div>
                    <p>Mon Grenier</p>
                </a>
            </div>
        </div>
    </div>
    <div class="recommendation-button">
        <a href="/components/quizz/4_questions.php">
            <button>Affiner mes recommandations</button>
        </a>
    </div>


</div>
<?php include __DIR__ . '/components/footer.php'; ?>
</body>
</html>
