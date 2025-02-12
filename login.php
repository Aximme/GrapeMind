<?php
session_start();
include 'components/header.php';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="css/registration.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/login.js"></script>
</head>
<body>
<main>
    <div class="form-container">
        <h2>Connectez-vous</h2>

        <form id="login-form">
            <label for="email">Adresse mail</label>
            <input type="email" id="email" name="email" required>
            <span id="email-error" class="error-message"></span><br>

            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required>
            <span id="password-error" class="error-message"></span><br>

            <div class="remember-me">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Se souvenir de moi</label>
            </div>

            <button type="submit" disabled>Se connecter</button>
        </form>

        <p class="forgot-password">
            <a href="forgot_password.php">Mot de passe oublié?</a>
        </p>
        <p class="sign-up">
            Vous n'avez pas de compte ? <a href="registration.php">Inscrivez-vous ici</a>
        </p>
    </div>
</main>
<?php include __DIR__ . '/components/footer.php'; ?>
</body>
</html>
