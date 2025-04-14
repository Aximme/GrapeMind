<!-- AJAX pour l’inscription d’un nouvel utilisateur

Contenu :
- Vérifie les champs, email unique, validité, confirmation mdp.
- Hash le mot de passe et créé un nouvel utilisateur dans la bdd.
- Confirmation JSON.

Utilisation :
- Appelé depuis registration.js

Dépendances :
- db.php
-->

<?php
global $conn;
require_once('db.php');

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
} else {
    echo json_encode(['success' => false, 'error' => 'Erreur lors de la création du compte.']);
}
$stmt->close();
