<?php
include 'components/header.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="css/registration.css">
    <!--LOADER-->
    <script defer src="/GrapeMind/js/loader.js"></script>
</head>
<body>
<main>
    <div class="form-container">
        <h2>Connectez-vous</h2>
        <form action="" method="post">
            <label for="email">Adresse mail ou nom utilisateur</label>
            <input type="text" id="email" name="email" required>

            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required>

            <div class="remember-me">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Se souvenir de moi</label>
            </div>

            <button type="submit">Se connecter</button>

            <p class="forgot-password">
                <a href="#">Mot de passe oublié?</a>
            </p>
            <p class="sign-up">
                Vous n'avez pas de compte ? <a href="/GrapeMind/registration.php">Inscrivez-vous ici</a>
            </p>
        </form>
    </div>
</main>
</body>
</html>
