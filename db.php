<?php
$host = 'localhost';
$username = 'root';
$password = '';

// Détecter MAMP en vérifiant plusieurs variations possibles
if (strpos(strtolower($_SERVER['DOCUMENT_ROOT']), 'mamp') !== false) {
    $password = 'root';  // Mot de passe pour MAMP
}

$conn = new mysqli($host, $username, $password, 'grape-mind');
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}
