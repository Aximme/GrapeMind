<?php

global $conn;
require_once __DIR__ . ('/db.php');

if (isset($_GET['email'])) {
    $email = htmlspecialchars($_GET['email']);

    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    echo json_encode(['exists' => $count > 0]);
    exit;
}
echo json_encode(['error' => 'Paramètre email manquant.']);

