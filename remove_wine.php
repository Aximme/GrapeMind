<?php
session_start();
global $conn;

include __DIR__ . '/db.php';

if (!isset($_SESSION['user']['id'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['user']['id'];
$idwine = isset($_POST['idwine']) ? intval($_POST['idwine']) : 0;
$context = isset($_POST['context']) ? $_POST['context'] : ''; // "cave" ou "grenier"
$type = isset($_POST['type']) ? $_POST['type'] : ''; // "real" ou "wishlist" (uniquement pour la cave)

// Vérifier si les paramètres sont valides
if ($idwine <= 0 || !in_array($context, ['cave', 'grenier'])) {
    header("Location: cave.php?error=invalid_request");
    exit;
}

// Déterminer la requête SQL selon le contexte
if ($context === "cave" && in_array($type, ['real', 'wishlist'])) {
    $query = $conn->prepare("DELETE FROM cave WHERE idwine = ? AND id_user = ? AND type = ?");
    $query->bind_param("iis", $idwine, $id_user, $type);
    $redirect_url = "cave.php?success=deleted&type=" . urlencode($type);
} elseif ($context === "grenier") {
    $query = $conn->prepare("DELETE FROM grenier WHERE idwine = ? AND id_user = ?");
    $query->bind_param("ii", $idwine, $id_user);
    $redirect_url = "grenier.php?success=deleted";
} else {
    header("Location: cave.php?error=invalid_type");
    exit;
}

// Exécuter la suppression et rediriger
if ($query->execute()) {
    header("Location: " . $redirect_url);
    exit;
} else {
    header("Location: cave.php?error=delete_failed");
    exit;
}
?>
