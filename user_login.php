<!-- AJAX pour la connexion utilisateur

Contenu :
- Vérifie les identifiants (email + mdp)
- Si ok, on crée la session utilisateur + update last_login.
- Renvoie réponse JSON (succès ou erreur).

Utilisation :
- Appelé depuis login.js

Dépendances :
- db.php
-->

<?php
session_start();
global $conn;
require_once('db.php');

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