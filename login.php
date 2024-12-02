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

        <?php
        $email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
        $password = isset($_POST['password']) ? ($_POST['password']) : '';

        if (isset($_GET['error'])) {
            echo '<p style="color:red;">' . htmlspecialchars($_GET['error']) . '</p>';
        }
        ?>

        <form action="user_login.php" method="post">
            <label for="email">Adresse mail ou nom utilisateur</label>
            <input type="email" id="email" name="email" value="<?php echo $email; ?>" ><br>


            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" value="<?php echo $password; ?>">
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
<?php include __DIR__ . '/components/footer.php'; ?>
</body>
</html>
