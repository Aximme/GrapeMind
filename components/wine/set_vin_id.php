<?php
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