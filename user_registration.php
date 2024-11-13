<?php
global $conn;
session_start();
require_once('db.php');

$username = isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '';
$email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
$address = isset($_POST['address']) ? htmlspecialchars($_POST['address']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

function rediriger($username, $email, $address, $erreur = '')
{
    $url = 'nouveau.php?username=' . urlencode($username) . '&email=' . urlencode($email) . '&address=' . urlencode($address);
    if (!empty($erreur)) {
        $url .= '&erreur=' . urlencode($erreur);
    }
    echo '<meta http-equiv="refresh" content="0;url=' . $url . '">';
    exit();
}

function verifierEmailUnique($conn, $email)
{
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_array()[0];
    return $count > 0;
}

function enregistrer($conn, $username, $email, $address, $password)
{
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, email, address, password) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Erreur de préparation : " . $conn->error);
    }

    $stmt->bind_param("ssss", $username, $email, $address, $hashedPassword);
    $stmt->execute();
}

if (!empty($username) && !empty($email) && !empty($address) && !empty($password) && !empty($confirmPassword)) {
    if (verifierEmailUnique($conn, $email)) {
        rediriger($username, $email, $address, "L'adresse e-mail est déjà utilisée.");
    }

    if ($password === $confirmPassword) {
        enregistrer($conn, $username, $email, $address, $password);
        $_SESSION['username'] = $username;
        echo '<meta http-equiv="refresh" content="0;url=index.php">';
    } else {
        rediriger($username, $email, $address, 'Les mots de passe ne correspondent pas.');
    }
} else {
    rediriger($username, $email, $address, 'Tous les champs sont obligatoires.');
}
?>
