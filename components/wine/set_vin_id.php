<?php
/*
    Gère la définition de l’id du vin à afficher via GET ou POST.

    Contenu :
    - Met à jour $_SESSION['vin_id'] selon la méthode.
    - Redirige vers la fiche détaillée du vin (wine-details.php).

    Utilisation :
    - Appelé avant wine-details.php pour initialiser l'id du vin.
    - Sécurise la valeur reçue (int) et gère les cas invalides.

    Dépendances :
    - wine-details.php
*/

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vin_id'])) {
    $_SESSION['vin_id'] = intval($_POST['vin_id']);
    echo "ID du vin mis à jour en session.";
    exit;
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $idwine = (int)$_GET['id'];

    $_SESSION['vin_id'] = $idwine;

    header("Location: wine-details.php");
    exit;
} else {
    header("Location: ../../index.php");
    exit;
}