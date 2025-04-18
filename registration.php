<!-- Page d'inscription utilisateur
 
Contenu :
- Formulaire de création de compte avec validation JS.
- Champs requis : nom, email, adresse, mot de passe + confirmation.
- Redirige vers login si déjà inscrit.

Dépendances :
- db.php, registration.js, registration.css, jQuery, header/footer
-->

<?php
session_start();
require_once 'db.php';
include 'components/header.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte</title>
    <link rel="stylesheet" href="css/registration.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/registration.js"></script>
</head>
<body>
<main>
    <div class="form-container">
        <h2>Créer un compte</h2>
        <p>Pour profiter pleinement de l'expérience!</p>

        <form id="registration-form">
            <label for="username">Nom d'utilisateur</label>
            <input type="text" id="username" name="username" required>
            <span id="username-error" class="error-message"></span><br>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
            <span id="email-error" class="error-message"></span><br>

            <label for="address">Adresse postale</label>
            <input type="text" id="address" name="address" required>
            <span id="address-error" class="error-message"></span><br>

            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required>
            <span id="password-error" class="error-message"></span><br>

            <label for="confirm_password">Confirmation mot de passe</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
            <span id="confirm-password-error" class="error-message"></span><br>

            <button type="submit" disabled>Nous Rejoindre</button>

        </form>
        <p class="log-in">
            Déjà un compte?<a href="login.php"> Connectez-vous</a>
        </p>
    </div>
</main>


</body>
</html>
