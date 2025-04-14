<!-- Vérifie l'âge d’un utilisateur

Contenu :
- Définit la variable de session `age_verified` à true quand il a mit oui.

Utilisation :
- Appelé côté client après clic sur "Oui" dans le popup d'âge pur la première connexion.
-->

<?php
session_start();
$_SESSION['age_verified'] = true;
?>
