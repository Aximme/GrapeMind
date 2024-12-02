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
    <script defer src="/GrapeMind/js/loader.js"></script>
</head>
<body>
<main>
    <div class="form-container">
        <h2>Créer un compte</h2>
        <p>Pour profiter pleinement de l'expérience !</p>

        <?php
        $username = isset($_GET['username']) ? htmlspecialchars($_GET['username']) : '';
        $email = isset($_GET['mail']) ? htmlspecialchars($_GET['mail']) : '';
        $address = isset($_GET['address']) ? htmlspecialchars($_GET['address']) : '';
        $erreur = isset($_GET['erreur']) ? htmlspecialchars($_GET['erreur']) : '';

        if (!empty($erreur)) {
            echo "<p style='color:red;'>$erreur</p>";
        }
        ?>
        <form action="user_registration.php" method="post">
            <label for="username">Nom d'Utilisateur</label>
            <input type="text" id="username" name="username" value="<?php echo $username; ?>"><br>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo $email; ?>"><br>

            <label for="address">Adresse Postale</label>
            <input type="text" id="address" name="address" value="<?php echo $address; ?>"><br>

            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" ><br>

            <label for="confirm_password">Confirmation mot de passe</label>
            <input type="password" id="confirm_password" name="confirm_password" ><br>

            <button type="submit">Nous Rejoindre</button>

            <p class="log-in">
                Déjà un compte ?<a href="/GrapeMind/login.php"> Connectez-vous</a>
            </p>
        </form>
    </div>
</main>
<?php include __DIR__ . '/components/footer.php'; ?>
</body>
</html>
