<!-- Réinitialisation de mot de passe via un token

Contenu :
- Vérifie la validité du token reçu.
- Si valide, on met à jour le mot de passe.
- Affiche un message de confirmation/erreur.

Utilisation :
- Accès uniquement via lien reçu par email (forgot_password.php).
-->

<?php
global $conn;
require 'db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expires > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
        $stmt->bind_param("si", $new_password, $user['id']);
        $stmt->execute();

        $message = "<p class='success'> Votre mot de passe a été mis à jour.</p>";
    } else {
        $message = "<p class='error'> Token invalide ou expiré.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réinitialiser le mot de passe - GrapeMind</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        h1 {
            margin-bottom: 20px;
            color: #973232;
        }
        form input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
        }
        form button {
            background-color: #973232;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        form button:hover {
            background-color: #973232;
        }
        .success {
            color: green;
            margin-bottom: 20px;
        }
        .error {
            color: red;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Réinitialiser le mot de passe</h1>
    <?= $message ?>
    <form method="post">
        <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token'] ?? '') ?>">
        <input type="password" name="password" placeholder="Nouveau mot de passe" required>
        <button type="submit">Réinitialiser</button>
    </form>
</div>
</body>
</html>
