<!--
    500.php

    Page d’erreur affichée lorsqu'une erreur interne du serveur empêche le chargement de la ressource demandée.

    Description :
    - Signale une erreur 500 (Internal Server Error).
    - Affiche un message clair à l’utilisateur avec un lien de retour vers l’accueil.

    Utilisation :
    - Utilisée par défaut lors d’une erreur critique du serveur.
    - Peut être appelée manuellement en cas de panne côté backend.

    Ressources utilisées :
    - Image statique située dans /GrapeMind/assets/images/error.png
    - Aucune interaction avec la base de données ou scripts côté serveur.
-->

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur interne du serveur - Erreur 500</title>
    <style>
        .error {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
        }
        h1 {
            font-size: 50px;
            color: #721c24;
        }
        p {
            font-size: 20px;
            color: #6c757d;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="error">
    <img src="/GrapeMind/assets/images/error.png" alt="Error image">
<h1>500</h1>
<p>Oups ! Quelque chose s'est mal passé de notre côté.</p>
<p>Veuillez réessayer plus tard ou <a href="/GrapeMind/index.php">retourner à la page d'accueil</a>.</p>
</div>
</body>
</html>
