<?php
session_start();
include 'components/header.php';
include 'db.php';

global $conn;

// Selection de 15 vins random pour le caroussel
$sql = "SELECT name, thumb, price FROM scrap ORDER BY RAND() LIMIT 15";
$result = $conn->query($sql);

$wines = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $wines[] = $row;
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/checkAdult.css">
    <link rel="stylesheet" href="css/filter-wine-index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.6.1/nouislider.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.6.1/nouislider.min.js"></script>


</head>
<body class="<?= isset($_SESSION['age_verified']) ? '' : 'blur' ?>">

<div id="scene-container">
    <div id="loading">Chargement du modèle...</div>
</div>
<div class="search-bar-wrapper">
    <div class="search-bar-container">
        <?php include 'components/search_bar.php'; ?>
    </div>
</div>
<div class="wine-selection">
    <h1>🍷 Trouver le vin qui me correspond</h1>

    <div class="wine-options">
        <label><input type="checkbox" value="Red"> Rouge <span class="red-circle"></span></label>
        <label><input type="checkbox" value="Rosé"> Rosé <span class="rose-circle"></span></label>
        <label><input type="checkbox" value="White"> Blanc <span class="white-circle"></span></label>
        <label><input type="checkbox" value="Sparkling"> Bulle <span class="bubble-circle"></span></label>
    </div>


    <div class="price-range-wrapper">
        <span>10</span>
        <div id="price-slider" class="range-slider"></div>
        <span>500+€</span>
    </div>
    <script src="js/price-filter.js"></script>



    <div class="bio-option">
        <label><input type="checkbox"> Agriculture biologique</label>
    </div>

    <button class="search-button_price" onclick="applyFilters()">Chercher selon mes goûts</button>
</div>



<?php if (!isset($_SESSION['age_verified'])): ?>
    <div class="age-popup" id="age-popup">
        <div class="age-popup-content">
            <p>Ce site contient des informations sur des produits alcoolisés.<br><br>Vous devez avoir 18 ans ou plus pour accéder à ce site, conformément à la législation en vigueur.<br></p>
            <h2>Avez-vous plus de 18 ans ?</h2>
            <button class="button-yes" id="yes-button">Oui</button>
            <button class="button-no" id="no-button">Non</button>
        </div>
    </div>
<?php endif; ?>

<script async src="https://unpkg.com/es-module-shims/dist/es-module-shims.js"></script>
<script type="importmap">
    {
        "imports": {
            "three": "https://unpkg.com/three@0.159.0/build/three.module.js",
            "three/addons/": "https://unpkg.com/three@0.159.0/examples/jsm/"
        }
    }
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dat-gui/0.7.7/dat.gui.min.js"></script>
<script type="module" src="js/3d-winebottle.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const agePopup = document.getElementById('age-popup');
        const yesButton = document.getElementById('yes-button');
        const noButton = document.getElementById('no-button');

        <?php if (!isset($_SESSION['age_verified'])): ?>
        agePopup.style.display = 'flex';
        <?php endif; ?>

        yesButton.addEventListener('click', function () {
            fetch('user-verify_age.php')
                .then(() => {
                    agePopup.style.display = 'none';
                    document.body.classList.remove('blur');
                });
        });

        noButton.addEventListener('click', function () {
            window.location.href = 'https://www.legifrance.gouv.fr/codes/article_lc/LEGIARTI000031927682';
        });
    });
</script>



<script src="js/index_carrousel.js"></script>
<h2 class="carousel-title">Recommandations Personnalisées</h2>
<div class="carousel-container">
    <button class="carousel-button left" onclick="previousSlide()">❮</button>
    <div class="carousel">
        <div class="carousel-track">
            <?php foreach ($wines as $wine): ?>
                <div class="carousel-item">
                    <img src="<?php echo htmlspecialchars($wine['thumb']); ?>" alt="<?php echo htmlspecialchars($wine['name']); ?>" class="wine-thumbnail">
                    <div class="wine-details">
                        <h3><?php echo htmlspecialchars($wine['name']); ?></h3>
                        <p class="wine-price"><?php echo htmlspecialchars($wine['price']); ?> €</p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <button class="carousel-button right" onclick="nextSlide()">❯</button>
</div>

</body>
</html>
