<?php
include 'components/header.php';



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="/css/checkAdult.css">
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (!localStorage.getItem("ageVerified")) {
                document.getElementById("age-popup").style.display = "flex";
                document.body.classList.add("blur");
            }
        });

        function handleAgeVerification(isAdult) {
            if (isAdult) {
                localStorage.setItem("ageVerified", "true");
                document.getElementById("age-popup").style.display = "none";
                document.body.classList.remove("blur");
            } else {
                window.location.href = "https://www.legifrance.gouv.fr/codes/article_lc/LEGIARTI000031927682";
            }
        }
    </script>
</head>
<body>

<div id="age-popup" class="age-popup">
    <div class="age-popup-content">
        <p>Ce site contient des informations sur des produits alcoolisés. Vous devez avoir 18 ans ou plus pour accéder à ce site, conformément à la législation en vigueur.</p>
        <h3>Avez-vous plus de 18 ans ?</h3>
        <button class="button-yes" onclick="handleAgeVerification(true)">Oui</button>
        <button class="button-no" onclick="handleAgeVerification(false")">Non</button>
    </div>
</div>

<footer>
    <p>&copy; 2023 Your Company Name. All rights reserved.</p>
</footer>

</body>
</html>
