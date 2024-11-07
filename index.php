<?php
include 'components/header.php';
include 'components/search_bar.php'

/*include 'components/footer.php';*/


?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/index-main.css">
    <link rel="stylesheet" href="css/checkAdult.css">

</head>
<body class="blur">

<div id="scene-container">
    <div id="loading">Chargement du modèle...</div>
</div>

<div class="age-popup" id="age-popup">
    <div class="age-popup-content">
        <p>Ce site contient des informations sur des produits alcoolisés.<br><br>Vous devez avoir 18 ans ou plus pour accéder à ce site, conformément à la législation en vigueur.<br></p>
        <h2>Avez-vous plus de 18 ans ?</h2>
        <button class="button-yes" id="yes-button">Oui</button>
        <button class="button-no" id="no-button">Non</button>
    </div>
</div>


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

        agePopup.style.display = 'flex';

        yesButton.addEventListener('click', function () {
            agePopup.style.display = 'none';
            document.body.classList.remove('blur');
        });

        noButton.addEventListener('click', function () {
            window.location.href = 'https://www.legifrance.gouv.fr/codes/article_lc/LEGIARTI000031927682';
        });
    });
</script>
</body>
</html>
