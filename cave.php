<?php
session_start();
global $conn;

include __DIR__ . '/components/header.php';
include __DIR__ . '/db.php';

if (!isset($_SESSION['user']['id'])) {
    echo "<p>Veuillez vous connecter pour voir votre cave.</p>";
    exit;
}

$id_user = $_SESSION['user']['id'];

// Récupérer les vins de la cave réelle
$query_real = $conn->prepare("
    SELECT descriptifs.idwine, scrap.thumb, scrap.name, scrap.price, scrap.flavorGroup_1, scrap.flavorGroup_2, scrap.flavorGroup_3, scrap.average_rating 
    FROM cave 
    JOIN descriptifs ON cave.idwine = descriptifs.idwine 
    JOIN scrap ON scrap.idwine = descriptifs.idwine
    WHERE cave.id_user = ? AND cave.type = 'real'
");
$query_real->bind_param("i", $id_user);
$query_real->execute();
$result_real = $query_real->get_result();

// Récupérer les vins de la liste d'envie
$query_wishlist = $conn->prepare("
    SELECT descriptifs.idwine, scrap.thumb, scrap.name, scrap.price, scrap.flavorGroup_1, scrap.flavorGroup_2, scrap.flavorGroup_3, scrap.average_rating 
    FROM cave 
    JOIN descriptifs ON cave.idwine = descriptifs.idwine 
    JOIN scrap ON scrap.idwine = descriptifs.idwine
    WHERE cave.id_user = ? AND cave.type = 'wishlist'
");
$query_wishlist->bind_param("i", $id_user);
$query_wishlist->execute();
$result_wishlist = $query_wishlist->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ma Cave</title>
    <link rel="stylesheet" href="css/search_results.css">
    <link rel="stylesheet" href="css/cave.css">
</head>
<body>
<div class="search-results-container">
    <h1>Ma Cave</h1>

    <label for="cave-type"></label>
    <select id="cave-type">
        <option value="real">Cave réelle</option>
        <option value="wishlist">Liste d'envie</option>
    </select>

    <?php if (isset($_GET['success']) && $_GET['success'] == 'deleted'): ?>
        <p class="success-message">Le vin a été supprimé avec succès !</p>
    <?php endif; ?>

    <div id="real-cave" class="search-results">
        <h2>Vins possédés</h2>
        <?php while ($row = $result_real->fetch_assoc()): ?>
            <div class="result-item">
                <a href="javascript:void(0);" onclick="setVinId(<?php echo $row['idwine']; ?>)" class="result-item-link">
                    <img src="<?php echo $row['thumb']; ?>" alt="<?php echo $row['name']; ?>" class="wine-thumbnail">
                    <div class="wine-details">
                        <h2><?php echo $row['name']; ?></h2>
                        <p class="wine-price">Prix : <?php echo number_format($row['price'], 2); ?> €</p>
                        <p class="wine-taste">Goûts :
                            <?php echo $row['flavorGroup_1']; ?>,
                            <?php echo $row['flavorGroup_2']; ?>,
                            <?php echo $row['flavorGroup_3']; ?>
                        </p>
                    </div>
                </a>
                <div class="delete-container">
                    <form method="POST" action="remove_wine.php" class="delete-form">
                        <input type="hidden" name="idwine" value="<?php echo $row['idwine']; ?>">
                        <input type="hidden" name="context" value="cave">
                        <input type="hidden" name="type" value="real">
                        <button type="submit" style="background: none; border: none; padding: 0; position: relative;">
                            <img src="assets/images/cross.png" alt="Supprimer" class="delete-icon">
                            <span class="tooltip">Supprimer le vin de la cave</span>
                        </button>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <div id="wishlist-cave" class="search-results" style="display: none;">
        <h2>Liste d'envie</h2>
        <?php while ($row = $result_wishlist->fetch_assoc()): ?>
            <div class="result-item">
                <a href="javascript:void(0);" onclick="setVinId(<?php echo $row['idwine']; ?>)" class="result-item-link">
                    <img src="<?php echo $row['thumb']; ?>" alt="<?php echo $row['name']; ?>" class="wine-thumbnail">
                    <div class="wine-details">
                        <h2><?php echo $row['name']; ?></h2>
                        <p class="wine-price">Prix : <?php echo number_format($row['price'], 2); ?> €</p>
                        <p class="wine-taste">Goûts :
                            <?php echo $row['flavorGroup_1']; ?>,
                            <?php echo $row['flavorGroup_2']; ?>,
                            <?php echo $row['flavorGroup_3']; ?>
                        </p>
                    </div>
                </a>
                <div class="delete-container">
                    <form method="POST" action="remove_wine.php" class="delete-form">
                        <input type="hidden" name="idwine" value="<?php echo $row['idwine']; ?>">
                        <input type="hidden" name="context" value="cave">
                        <input type="hidden" name="type" value="wishlist">
                        <button type="submit" style="background: none; border: none; padding: 0; position: relative;">
                            <img src="assets/images/cross.png" alt="Supprimer" class="delete-icon">
                            <span class="tooltip">Supprimer le vin de la cave</span>
                        </button>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const selectElement = document.getElementById("cave-type");
        const realCave = document.getElementById("real-cave");
        const wishlistCave = document.getElementById("wishlist-cave");


        const urlParams = new URLSearchParams(window.location.search);
        const selectedType = urlParams.get("type") || "real"; // Par défaut : real

        // Appliquer la bonne vue
        if (selectedType === "wishlist") {
            realCave.style.display = "none";
            wishlistCave.style.display = "block";
            selectElement.value = "wishlist";
        } else {
            realCave.style.display = "block";
            wishlistCave.style.display = "none";
            selectElement.value = "real";
        }


        selectElement.addEventListener("change", function () {
            if (selectElement.value === "real") {
                window.location.href = "cave.php?type=real";
            } else {
                window.location.href = "cave.php?type=wishlist";
            }
        });
    });

    document.addEventListener("DOMContentLoaded", function () {
        const message = document.querySelector(".success-message");
        if (message) {
            setTimeout(() => {
                message.style.opacity = "0";
                setTimeout(() => message.style.display = "none", 500);
            }, 3000);
        }
    });
</script>

<!-- Fonction JavaScript pour envoyer l'ID du vin via POST et rediriger -->
<script>
    function setVinId(vinId) {
        fetch("/GrapeMind/components/wine/set_vin_id.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "vin_id=" + vinId
        })
            .then(response => response.text())
            .then(data => {
                console.log(data);
                window.location.href = "/GrapeMind/components/wine/wine-details.php";
            })
            .catch(error => console.error("Erreur lors de l'envoi de l'ID :", error));
    }
</script>

<?php include __DIR__ . '/components/footer.php'; ?>
</body>
</html>
