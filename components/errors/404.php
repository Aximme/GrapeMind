<!--
    Page d’erreur affichée lorsque l’utilisateur tente d’accéder à une ressource inexistante sur le site.

    Description :
    - Affiche un message personnalisé pour signaler l'erreur 404.
    - Propose un lien pour revenir à la page d'accueil.

    Utilisation :
    - Placée dans le dossier public/pages d’erreurs ou équivalent.
    - Déclenchée automatiquement par le serveur ou manuellement via redirection.

    Ressources utilisées :
    - Image statique située dans /GrapeMind/assets/images/error.png
    - Aucune dépendance serveur ou base de données.
-->

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page non trouvée - Erreur 404</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
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
<img src="/GrapeMind/assets/images/error.png" alt="Error image">
<h1>404</h1>
<p>Oups ! La page que vous cherchez n'existe pas.</p>
<p><a href="/GrapeMind/index.php">Retour à la page d'accueil</a></p>
</body>
</html>
