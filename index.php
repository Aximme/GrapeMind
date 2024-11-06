<?php
include 'components/header.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/index-main.css">

</head>
<body>

<div id="scene-container">
    <div id="loading">Chargement du modèle...</div>
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
</body>
</html>