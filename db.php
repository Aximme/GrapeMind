<?php
$servername = "localhost";
$username = "root"; // Par défaut sur une installation locale, c'est souvent 'root'
$password = "root"; // Souvent, il n'y a pas de mot de passe pour 'root' sur une configuration locale
$dbname = "grape-mind";

// Créer une connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}
echo "Connexion réussie !";
?>
