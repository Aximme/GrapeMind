<?php
session_start();

if (isset($_POST['vin_id'])) {
    $_SESSION['vin_id'] = intval($_POST['vin_id']);
    echo "ID du vin mis à jour en session.";
} else {
    echo "Aucun ID de vin reçu.";
}
