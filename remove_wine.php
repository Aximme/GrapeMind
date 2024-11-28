<?php
global $conn;
session_start();
require_once __DIR__ . '/db.php';

if (!isset($_SESSION['user']['id'])) {
    echo "<p>Erreur : Vous devez être connecté pour effectuer cette action.</p>";
    exit;
}

$id_user = $_SESSION['user']['id'];


$idwine = isset($_POST['idwine']) ? intval($_POST['idwine']) : null;
$context = isset($_POST['context']) ? $_POST['context'] : null;

if (!$idwine || !$context || !in_array($context, ['cave', 'grenier'])) {
    echo "<p>Erreur : données invalides.</p>";
    exit;
}


$table = ($context === 'cave') ? 'cave' : 'grenier';


$query = $conn->prepare("DELETE FROM $table WHERE idwine = ? AND id_user = ?");
if (!$query) {
    echo "<p>Erreur dans la préparation de la requête : " . $conn->error . "</p>";
    exit;
}

$query->bind_param("ii", $idwine, $id_user);

if ($query->execute()) {
    header("Location: $context.php?success=1");
    exit;
} else {
    echo "<p>Erreur lors de la suppression : " . $query->error . "</p>";
}
?>

