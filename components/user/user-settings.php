<?php
include __DIR__ . '/../header.php';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1, width=device-width">
    <link rel="stylesheet" href="/GrapeMind/css/user/user-settings.css" />
    <script>
        function confirmDeletion() {
            if (confirm("Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.")) {
//TODO : ajouter ici les fonctions php, ou faire en js pour supprimer le compte.
            alert("Compte supprimé avec succès.");
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
                <div class="user-section">
                    <div class="label-wrapper">
                        <label class="label" for="username">Nom d’Utilisateur</label>
                    </div>
                    <div class="input-wrapper">
                        <input type="text" id="username" class="text-field" placeholder="Entrez votre nouveau nom d'utilisateur">
                        <div class="button-component">
                            <img class="icon" alt="edit-icon" src="../../assets/images/edit.png">
                        </div>
                    </div>
                </div>

                <div class="user-section">
                    <div class="label-wrapper">
                        <label class="label" for="email">Adresse Mail</label>
                    </div>
                    <div class="input-wrapper">
                        <input type="email" id="email" class="text-field" placeholder="Entrez votre nouvelle adresse mail">
                        <div class="button-component">
                            <img class="icon" alt="edit-icon" src="../../assets/images/edit.png">
                        </div>
                    </div>
                </div>

                <div class="user-section">
                    <div class="label-wrapper">
                        <label class="label" for="password">Mot de passe</label>
                    </div>
                    <div class="input-wrapper">
                        <input type="password" id="password" class="text-field" placeholder="Entrez votre nouveau mot de passe">
                        <div class="button-component">
                            <img class="icon" alt="edit-icon" src="../../assets/images/edit.png">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-avatars-26-parent">
            <img class="d-avatars-26" alt="default-profile-picture" src="../../assets/images/default-pp.png">
            <h2>Username</h2>
            <div class="label3">
                <img class="upload-icon" alt="upload-icon" src="../../assets/images/upload.png">
                Modifier ma photo de profil
            </div>
        </div>
    </div>

    <div class="delete-account" onclick="confirmDeletion()">
        Supprimer mon Compte
    </div>
</div>

</body>
</html>
