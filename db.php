<!-- Fichier principal de connexion à la BDD MySQL

Contenu :
- Charge les variables d’environnement depuis .env avec Dotenv.
- Crée une instance mysqli stockée dans $conn.
- S’adapte à l’environnement local (W10/11 & MacOS | MAMP/WAMP)

Utilisation :
- Inclure dans tous les fichiers nécessitant une connexion à la bdd.

Dépendances :
- php-dotenv (via composer)
-->

<?php
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$host = $_ENV['DB_HOST'] ?? 'localhost';
$username = $_ENV['DB_USERNAME'] ?? 'root';
$password = $_ENV['DB_PASSWORD'] ?? '';
$database = $_ENV['DB_NAME'] ?? 'grape-mind';

if (empty($password) && strpos(strtolower($_SERVER['DOCUMENT_ROOT']), 'mamp') !== false) {
    $password = 'root';
}

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}
