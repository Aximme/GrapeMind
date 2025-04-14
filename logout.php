<!-- Gère la déconnexion utilisateur

Contenu :
- Supprime la session en cours et redirige vers l’index.

Utilisation :
- Appélé lorsqu’un user clique sur "Deconnexion".
-->

<?php
session_start();
session_unset();
session_destroy();

echo '<meta http-equiv="refresh" content="0;URL=index.php">';
exit();
?>
