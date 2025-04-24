<!--
     400.php
 
     Page d’erreur affichée lorsqu’une requête invalide a été envoyée au serveur.
 
     Description :
     - Signale une erreur 400 (Bad Request).
     - Affiche un message à l’utilisateur avec un lien de retour vers l'index.
 
 
     Ressources utilisées :
     - Image -> /images/error.png
 -->
 
 <!DOCTYPE html>
 <html lang="fr">
 <head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Requête invalide - Erreur 400</title>
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
     <img src="/assets/images/error.png" alt="Error image">
     <h1>400</h1>
     <p>La requête envoyée est invalide ou mal formulée.</p>
     <p>Merci de vérifier l’URL ou les données envoyées, ou <a href="/GrapeMind/index.php">retourner à l'accueil</a>.</p>
 </div>
 </body>
 </html>