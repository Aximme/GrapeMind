<!-- Page de présentation du projet et de l'équipe de GrapeMind

Contenu :
- Informations sur l’équipe et le cadre universitaire du projet.
- Coordonnées et carte d’emplacement de la fac (ifram Gmaps).

Utilisation :
- Accessible a tous sans connexion nécessaire.

Dépendances :
- components/header.php, components/footer.php, css/main.css, loader.js
-->

<?php
include 'components/header.php';
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>À propos</title>
    <link rel="stylesheet" href="css/main.css">
    <!--LOADER-->
    <script defer src="/GrapeMind/js/loader.js"></script>
</head>
<body>

<main class="about-container">
    <h1 class="about-title">A PROPOS DE NOUS</h1>

    <div class="cards">
        <!-- Carte "Notre Équipe" -->
        <div class="card">
            <h2 class="card-title">Notre Équipe</h2>
            <p>
                Étudiants en 3ème année de licence MIASHS à l’université Paul Valéry.<br>
                Dans le cadre de notre enseignement "Gestion de Projet" nous avons élaboré ce site internet de recommandation et de suggestions autour du vin.
            </p>
            <div class="team-picture">
                <img src="/GrapeMind/assets/images/team-degustation.jpg" alt="Team Picture" class="team-photo">
            </div>
        </div>

        <!-- Carte "Nous Contacter" -->
        <div class="card">
            <h2 class="card-title">Nous Contacter</h2>
            <div class="contact-info">
                <p><strong>Email :</strong> info[at]grapemind.fr</p>
                <p>Université Paul Valéry, Montpellier 3</p>
                <hr class="separator">
                <!-- Google Map Iframe -->
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2887.7452420710115!2d3.867529411068972!3d43.63266105363944!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x12b6af217be1ff51%3A0x26d70bd15039af4c!2sUniversit%C3%A9%20Paul-Val%C3%A9ry%20Montpellier%203!5e0!3m2!1sfr!2sfr!4v1729842943368!5m2!1sfr!2sfr" width="100%" height="200" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </div>
</main>
<?php include __DIR__ . '/components/footer.php'; ?>
</body>
</html>

