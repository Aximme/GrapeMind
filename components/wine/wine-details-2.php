<?php
/*
    Récupère les données complètes d’un vin depuis la bdd via son id (stocké/recupéré en session).

    Contenu :
    - Jointure entre tables `descriptifs` et `scrap` pour récupérer tous les détails : (nom, type, cépages, arômes, prix, ...)
    - Stocke le résultat dans $row pour affichage dans wine-details.php.

    Utilisation :
    - Inclus dans wine-details.php.
    - Nécessite que $_SESSION['vin_id'] soit défini en amont.

    Requiert :
    - Connexion bdd via db.php
    - Tables : `descriptifs`, `scrap`
*/

session_start();

global $conn;
require_once '../../db.php';


$vin_id = isset($_SESSION['vin_id']) ? $_SESSION['vin_id'] : 111630;


$sql = "
    SELECT 
        d.idwine,
        d.Type,
        d.Elaborate,
        d.Grapes,
        d.Harmonize,
        d.ABV,
        d.Body,
        d.Acidity,
        d.Country,
        d.RegionName,
        d.WineryName,
        d.Vintages,
        s.name,
        s.link,
        s.thumb,
        s.average_rating,
        s.price,
        s.tasteKeywords_1,
        s.tasteMentions_1,
        s.flavorGroup_1,
        s.flavorGroup_2,
        s.flavorGroup_3
    FROM 
        descriptifs AS d
    LEFT JOIN 
        scrap AS s ON d.idwine = s.idwine
    WHERE 
        d.idwine = $vin_id
";


$result = $conn->query($sql);

$row = null;
if ($result && $result->num_rows > 0) {
    // Sortir les données
    $row = $result->fetch_assoc();
} else {
    echo "Aucun vin trouvé avec cet ID.";
}
?>
