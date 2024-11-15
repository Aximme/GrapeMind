<?php
global $conn;
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /GrapeMind/login.php");
    exit();
}
require_once __DIR__ . '/../../db.php'; // Chemin relatif vers db.php
include __DIR__ . '/../header.php'; // Chemin relatif vers header.php

$userId = $_SESSION['user']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($conn->connect_error) {
        die("Erreur de connexion : " . $conn->connect_error);
    }

    // maj username
    if (isset($_POST['update_username'])) {
        $newUsername = htmlspecialchars($_POST['username']);
        $stmt = $conn->prepare("UPDATE users SET username = ? WHERE id = ?");
        $stmt->bind_param("si", $newUsername, $userId);
        if ($stmt->execute()) {
            $_SESSION['user']['username'] = $newUsername;
            echo "<script>alert('Nom d\'utilisateur mis à jour avec succès !');</script>";
        } else {
            echo "<script>alert('Erreur lors de la mise à jour du nom d\'utilisateur.');</script>";
        }
        $stmt->close();
    }
    // maj email
    elseif (isset($_POST['update_email'])) {
        $newEmail = htmlspecialchars($_POST['email']);
        $stmt = $conn->prepare("UPDATE users SET email = ? WHERE id = ?");
        $stmt->bind_param("si", $newEmail, $userId);
        if ($stmt->execute()) {
            $_SESSION['user']['email'] = $newEmail;
            echo "<script>alert('Adresse e-mail mise à jour avec succès !');</script>";
        } else {
            echo "<script>alert('Erreur lors de la mise à jour de l\'adresse e-mail.');</script>";
        }
        $stmt->close();
    }
    // maj mdp
    elseif (isset($_POST['update_password'])) {
        $newPassword = htmlspecialchars($_POST['password']);
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashedPassword, $userId);
        if ($stmt->execute()) {
            echo "<script>alert('Mot de passe mis à jour avec succès !');</script>";
        } else {
            echo "<script>alert('Erreur lors de la mise à jour du mot de passe.');</script>";
        }
        $stmt->close();
    }
    // delete acount
    elseif (isset($_POST['delete_account'])) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        if ($stmt->execute()) {
            session_destroy();
            echo "<script>alert('Compte supprimé avec succès !'); window.location.href = '/GrapeMind/index.php';</script>";
        } else {
            echo "<script>alert('Erreur lors de la suppression du compte.');</script>";
        }
        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1, width=device-width">
    <link rel="stylesheet" href="/GrapeMind/css/user/user-settings.css"/>
    <script>
        function confirmDeletion() {
            if (confirm("Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.")) {
                document.getElementById("delete-account-form").submit();
            }
        }
    </script>
</head>
<body>

<div class="parametres">
    <div class="header-container">
        <img class="settings-icon" alt="" src="../../assets/images/settings.png">
        <div class="page-title">Paramètres du Compte</div>
    </div>

    <div class="form-container">
        <div class="form-fields">
            <div class="fields-wrapper">
                <form method="POST">
                    <div class="user-section">
                        <div class="label-wrapper">
                            <label class="label" for="username">Nom d’Utilisateur</label>
                        </div>
                        <div class="input-wrapper">
                            <input type="text" id="username" name="username" class="text-field"
                                   placeholder="Entrez votre nouveau nom d'utilisateur">
                            <button type="submit" name="update_username" class="button-component">
                                <img src="/GrapeMind/assets/images/edit.png" alt="Modifier" style="width:16px; height:16px;">
                            </button>
                        </div>
                    </div>
                </form>
                <form method="POST">
                    <div class="user-section">
                        <div class="label-wrapper">
                            <label class="label" for="email">Adresse Mail</label>
                        </div>
                        <div class="input-wrapper">
                            <input type="email" id="email" name="email" class="text-field"
                                   placeholder="Entrez votre nouvelle adresse mail">
                            <button type="submit" name="update_email" class="button-component">
                                <img src="/GrapeMind/assets/images/edit.png" alt="Modifier" style="width:16px; height:16px;">
                            </button>
                        </div>
                    </div>
                </form>
                <form method="POST">
                    <div class="user-section">
                        <div class="label-wrapper">
                            <label class="label" for="password">Mot de passe</label>
                        </div>
                        <div class="input-wrapper">
                            <input type="password" id="password" name="password" class="text-field"
                                   placeholder="Entrez votre nouveau mot de passe">
                            <button type="submit" name="update_password" class="button-component">
                                <img src="/GrapeMind/assets/images/edit.png" alt="Modifier" style="width:16px; height:16px;">
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="d-avatars-26-parent">
            <img class="d-avatars-26" alt="default-profile-picture" src="../../assets/images/default-pp.png">
            <h2><?php echo htmlspecialchars($_SESSION['user']['username']); ?></h2>
            <div class="label3">
                <img class="upload-icon" alt="upload-icon" src="../../assets/images/upload.png">
                Modifier ma photo de profil
            </div>
        </div>
    </div>
    <form id="delete-account-form" method="POST">
        <input type="hidden" name="delete_account" value="1">
        <div class="delete-account" onclick="confirmDeletion()">
            Supprimer mon Compte
        </div>
    </form>
</div>

</body>
</html>
