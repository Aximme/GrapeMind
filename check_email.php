<!-- Vérifier si un email est déjà utilisé.

Contenu :
- Reçoit un paramètre `email` en GET.
- Vérifie sa présence dans la table users.
- Renvoie une réponse JSON avec true/false selon la dispo ou non du mail.

Utilisation :
- Appelé côté client lors de l’inscription.

Dépendances :
- db.php
-->

<?php

global $conn;
require_once('db.php');

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

