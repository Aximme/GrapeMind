<?php
global $conn;
require_once __DIR__ . ('/db.php');
require_once 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

header('Content-Type: application/json');

$username = isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '';
$email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
$address = isset($_POST['address']) ? htmlspecialchars($_POST['address']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

if (empty($username) || empty($email) || empty($address) || empty($password) || empty($confirmPassword)) {
    echo json_encode(['success' => false, 'error' => 'Tous les champs sont obligatoires.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => "L'adresse e-mail n'est pas valide."]);
    exit;
}

$stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();

if ($count > 0) {
    echo json_encode(['success' => false, 'error' => "L'adresse e-mail est déjà utilisée."]);
    exit;
}

if ($password !== $confirmPassword) {
    echo json_encode(['success' => false, 'error' => 'Les mots de passe ne correspondent pas.']);
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO users (username, email, address, password) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $username, $email, $address, $hashedPassword);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);


    /* WEBHOOK DISCORD */
    $ip = $_SERVER['REMOTE_ADDR'];
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Inconnu';
    $hostname = gethostbyaddr($ip) ?: 'Inconnue';;
    $date = date("Y-m-d H:i:s");
    
    $webhookUrl = $_ENV['DSWEBHOOK_NEWACC'];
    
    $embed = [
        "title" => "🆕 Nouvel utilisateur inscrit",
        "color" => hexdec("00ff99"),
        "fields" => [
            ["name" => "👤 Nom d'utilisateur", "value" => $username, "inline" => true],
            ["name" => "📧 Email", "value" => $email, "inline" => true],
            ["name" => "📍 Adresse", "value" => $address, "inline" => false],
            ["name" => "🌐 Adresse IP", "value" => $ip, "inline" => true],
            ["name" => "💻 Navigateur", "value" => substr($userAgent, 0, 100), "inline" => false],
            ["name" => "🖥️ Hostname", "value" => $hostname, "inline" => false],
            ["name" => "🕐 Date", "value" => $date, "inline" => false],
        ],
        "footer" => [
            "text" => "Webhook de création de compte",
        ],
    ];
    
    $payload = json_encode(["embeds" => [$embed]]);
    $ch = curl_init($webhookUrl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);    


} else {
    echo json_encode(['success' => false, 'error' => 'Erreur lors de la création du compte.']);
}
$stmt->close();
