<?php
global $conn;
session_start();
require_once('db.php');

$email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if (empty($email) || empty($password)) {
    header("Location: login.php?error=Champs requis manquants");
    exit();
}

try {
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = array(
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'address' => $user['address'],
            );

            header("Location: index.php");
            exit();
        } else {
            header("Location: login.php?error=Adresse e-mail ou mot de passe incorrect");
            exit();
        }
    } else {
        header("Location: login.php?error=Adresse mail introuvable");
        exit();
    }

} catch (Exception $e) {
    echo "Erreur lors de la connexion : " . $e->getMessage();
    exit();
}
?>
