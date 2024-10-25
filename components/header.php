<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GrapeMind</title>
    <link rel="stylesheet" href="../css/main.css">
</head>
<body>
<header class="header">
    <div class="logo">
        <img src="../assets/images/image_header.png" alt="GrapeMind Logo">
        <span class="logo-text">GrapeMind</span>
    </div>
    <nav class="navigation">
        <a href="../index.php">Accueil</a>
        <a href="../components/wine_map/map-main.php">Carte</a>
        <a href="../events.php">Événements</a>
        <a href="../about.php">À propos</a>
    </nav>
    <div class="auth">
        <a href="../login.php" class="login">Se connecter</a>
        <a href="../registration.php" class="signup">S'inscrire</a>
    </div>
    <!-- Menu Déroulant -->
    <div class="menu-icon">
        <span id="menu-toggle">&#9776;</span>
    </div>
    <div id="dropdown-menu" class="dropdown-menu">
        <a href="#">MON PROFIL</a>
        <a href="#">STATISTIQUES</a>
        <a href="#">PARAMÈTRES DU COMPTE</a>
    </div>
    <script src="../js/menu_header.js"></script>
</header>
