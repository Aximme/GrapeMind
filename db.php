<?php
// Informations de connexion
$host = 'sy11eo.myd.infomaniak.com'; // Adresse de l'hôte MySQL sur Infomaniak
$username = 'sy11eo_test';      // Nom d'utilisateur pour la base de données
$password = 'Grapemind2024$';                      // Mot de passe (ajoutez-le ici si nécessaire)
$database = 'sy11eo_grapemind';            // Nom de la base de données

// Création de la connexion
$conn = new mysqli($host, $username, $password, $database);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Si tout va bien
// echo "Connexion réussie à la base de données";
?>