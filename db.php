<?php
require_once __DIR__ . '/vendor/autoload.php'; // Charger Composer et phpdotenv

// Charger le fichier .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Charger les variables d'environnement
$host = $_ENV['DB_HOST'] ?? 'Erreur';
$username = $_ENV['DB_USERNAME'] ?? 'Erreur';
$password = $_ENV['DB_PASSWORD'] ?? 'Erreur';
$dbname = $_ENV['DB_NAME'] ?? 'Erreur';

// Vérifier si les variables sont bien définies
if ($host === 'Erreur' || $username === 'Erreur' || $dbname === 'Erreur') {
    die("Erreur : Les variables d'environnement ne sont pas chargées.");
}

// Connexion à MySQL
$conn = new mysqli($host, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("La connexion à la base de données a échoué : " . $conn->connect_error);
}
?>
