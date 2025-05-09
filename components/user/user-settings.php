<!--
    Page de gestion des paramètres perso de l'user connecté.

    Contenu :
    - Permet de màj le nom d'utilisateur, email, mdp et photo de profil.
    - Possibilité de supprimer définitivement le compte.

    Utilisation :
    - Accessible uniquement si l'user est connecté (check via session php).
    - Requêtes vers la bdd pour appliquer les changements (table `users`).
    - Possibilité d'upload une photo de profil.

    Dépendances :
    - Connexion via db.php
    - header.php pour l'en-tête commun
    - CSS : /css/user/user-settings.css
    - Images : /assets/images
-->

<?php
global $conn;
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /GrapeMind/login.php");
    exit();
}
require_once __DIR__ . '/../../db.php';
include __DIR__ . '/../header.php';

$userId = $_SESSION['user']['id'];


$usernameMessage = '';
$emailMessage = '';
$passwordMessage = '';
$pictureMessage = '';
$deleteMessage = '';



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
            $usernameMessage = ['success' => "Nom d'utilisateur mis à jour avec succès !"];
        } else {
            $usernameMessage = ['error' => "Erreur lors de la màj de votre username."];
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
            $emailMessage = ['success' => "Email mis à jour avec succès !"];
        } else {
            $emailMessage = ['error' => "Erreur lors de la màj de votre email."];
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
            $_SESSION['user']['password'] = $hashedPassword;
            $passwordMessage = ['success' => "Mot de passe mis à jour avec succès !"];
        } else {
            $passwordMessage = ['error' => "Erreur lors de la màj de votre mot de passe."];
        }
        $stmt->close();
    }
    // maj photo de profil
    elseif (isset($_POST['update_profile_picture']) && isset($_FILES['profile_picture'])) {
        $file = $_FILES['profile_picture'];

        if ($file['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../assets/images/profile_pictures/';
            $fileName = $userId . '_' . basename($file['name']);
            $uploadFilePath = $uploadDir . $fileName;

            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($file['type'], $allowedTypes)) {
                if (move_uploaded_file($file['tmp_name'], $uploadFilePath)) {
                    $profilePicturePath = "/GrapeMind/assets/images/profile_pictures/" . $fileName;

                    $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
                    $stmt->bind_param("si", $profilePicturePath, $userId);
                    if ($stmt->execute()) {
                        $_SESSION['user']['profile_picture'] = $profilePicturePath;
                        $pictureMessage = ['success' => "Photo de profil mise à jour avec succès !"];
                    } else {
                        $pictureMessage = ['error' => "Erreur lors de la màj de votre photo de profil."];
                    }
                    $stmt->close();
                } else {
                    echo "<script>alert('Erreur lors du téléchargement du fichier.');</script>";
                }
            } else {
                echo "<script>alert('Type de fichier non autorisé.');</script>";
            }
        } else {
            echo "<script>alert('Erreur lors de l\'upload de la photo.');</script>";
        }
    }
    // delete account
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
                        <?php if (!empty($usernameMessage)): ?>
                            <div class="<?= isset($usernameMessage['success']) ? 'input-success' : 'input-error' ?>">
                                <?= htmlspecialchars($usernameMessage['success'] ?? $usernameMessage['error']) ?>
                            </div>
                        <?php endif; ?>
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
                        <?php if (!empty($emailMessage)): ?>
                            <div class="<?= isset($emailMessage['success']) ? 'input-success' : 'input-error' ?>">
                                <?= htmlspecialchars($emailMessage['success'] ?? $emailMessage['error']) ?>
                            </div>
                        <?php endif; ?>
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
                        <?php if (!empty($passwordMessage)): ?>
                            <div class="<?= isset($passwordMessage['success']) ? 'input-success' : 'input-error' ?>">
                                <?= htmlspecialchars($passwordMessage['success'] ?? $passwordMessage['error']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </form>

                <div class="d-avatars-26-parent">
                    <img class="d-avatars-26" alt="photo de profil"
                        src="<?php echo isset($_SESSION['user']['profile_picture']) ? htmlspecialchars($_SESSION['user']['profile_picture']) : '../../assets/images/default-pp.png'; ?>">
                    <h2><?php echo htmlspecialchars($_SESSION['user']['username']); ?></h2>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="photo-upload-section">
                            <label for="profile_picture" class="upload-label">
                                <img class="upload-icon" alt="upload-icon" src="../../assets/images/upload.png">
                                Modifier ma photo de profil
                            </label>
                            <input type="file" id="profile_picture" name="profile_picture" accept="image/*" style="display: none;" onchange="this.form.submit();">
                            <input type="hidden" name="update_profile_picture">
                        </div>
                        <?php if (!empty($pictureMessage)): ?>
                            <div class="<?= isset($pictureMessage['success']) ? 'input-success' : 'input-error' ?>">
                                <?= htmlspecialchars($pictureMessage['success'] ?? $pictureMessage['error']) ?>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>


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