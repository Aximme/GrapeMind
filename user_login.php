<?php
session_start();
global $conn;
require_once __DIR__ .('/db.php');
require_once 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

header('Content-Type: application/json');

$email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'error' => 'Champs requis manquants']);
    exit();
}

try {
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => 'Erreur dans la requête SQL : ' . $conn->error]);
        exit();
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $current_time = date('Y-m-d H:i:s');
            $user_id = $user['id'];

            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'address' => $user['address'],
                'last_login' => $current_time,
            ];

            $update_sql = "UPDATE users SET last_login = NOW() WHERE email = ?";
            $update_stmt = $conn->prepare($update_sql);

            if (!$update_stmt) {
                echo json_encode(['success' => false, 'error' => 'Erreur dans la requête de mise à jour : ' . $conn->error]);
                exit();
            }

            $update_stmt->bind_param("s", $email);
            $update_stmt->execute();

            echo json_encode(['success' => true]);

            /* WEBHOOK DISCORD - Connexion utilisateur */
            $ip = $_SERVER['REMOTE_ADDR'];
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Inconnu';
            $hostname = gethostbyaddr($ip) ?: 'Inconnue';
            $date = date("Y-m-d H:i:s");

            $webhookUrl = $_ENV['DSWEBHOOK_NEWACC'] ?? null;

            if ($webhookUrl) {
                $embed = [
                    "title" => "✅ Nouvelle Connexion",
                    "color" => hexdec("3498db"),
                    "fields" => [
                        ["name" => "👤 Nom d'utilisateur", "value" => $user['username'], "inline" => true],
                        ["name" => "📧 Email", "value" => $user['email'], "inline" => true],
                        ["name" => "📍 Adresse", "value" => $user['address'], "inline" => false],
                        ["name" => "🌐 Adresse IP", "value" => $ip, "inline" => true],
                        ["name" => "💻 Navigateur", "value" => substr($userAgent, 0, 100), "inline" => false],
                        ["name" => "🖥️ Hostname", "value" => $hostname, "inline" => false],
                        ["name" => "🕐 Date", "value" => $date, "inline" => false],
                    ],
                    "footer" => [
                        "text" => "Webhook de connexion utilisateur",
                    ],
                ];

                $payload = json_encode(["embeds" => [$embed]]);

                $ch = curl_init($webhookUrl);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_exec($ch);
                curl_close($ch);
            }

        } else {
            echo json_encode(['success' => false, 'error' => 'Adresse e-mail ou mot de passe incorrect']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Adresse mail introuvable']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => "Erreur : " . $e->getMessage()]);
}
?>