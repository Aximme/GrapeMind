


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="">
    <title>Formulaire d'Enregistrement</title>
</head>
<body>

<header>
    <h1>Bienvenue sur notre site</h1>
</header>

<main>
    <section>
        <form action="" method="post" onsubmit="return validateForm()">
            <h2>Inscription</h2>

            <label for="nom">Nom:</label>
            <input type="text" id="nom" name="n" required><br><br>

            <label for="prenom">Prénom:</label>
            <input type="text" id="prenom" name="p" required><br><br>

            <label for="adresse">Adresse:</label>
            <input type="text" id="adresse" name="adr" required><br><br>

            <label for="numero">Numéro de téléphone:</label>
            <input type="tel" id="numero" name="num" required><br><br>

            <label for="email">Adresse e-mail:</label>
            <input type="email" id="email" name="mail" required><br><br>

            <label for="mdp1">Mot de passe:</label>
            <input type="password" id="mdp1" name="mdp1" required><br><br>

            <label for="mdp2">Confirmer votre mot de passe:</label>
            <input type="password" id="mdp2" name="mdp2" required><br><br>

            <input type="submit" value="S'inscrire">
        </form>
    </section>
</main>
</body>
</html>



