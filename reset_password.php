<?php
global$conn; require 'db.php';

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

        echo "✅ Votre mot de passe a été mis à jour.";
    } else {
        echo "❌ Token invalide ou expiré.";
    }
}
?>
<form method="post">
<input type="hidden" name="token" value="<?= $_GET['token'] ?>">
<input type="password" name="password" placeholder="Nouveau mot de passe" required>
<button type="submit">Réinitialiser</button>
</form>
