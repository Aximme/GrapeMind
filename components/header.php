<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GrapeMind</title>
    <link rel="stylesheet" href="/GrapeMind/css/main.css">
</head>
<body>
<header class="header">
    <div class="logo">

        <img src="/GrapeMind/assets/images/image_header.png" alt="GrapeMind Logo">

        <span class="logo-text">GrapeMind</span>
    </div>
    <nav class="navigation">

        <a href="/GrapeMind/index.php">Accueil</a>
        <a href="/GrapeMind/components/wine_map/map-main.php">Carte</a>
        <a href="/GrapeMind/events.php">Événements</a>
        <a href="/GrapeMind/about.php">À propos</a>
    </nav>
    <div class="auth">
        <?php if (!isset($_SESSION['user'])): ?>
            <a href="/GrapeMind/login.php" class="login">Se connecter</a>
            <a href="/GrapeMind/registration.php" class="signup">S'inscrire</a>
        <?php else: ?>
            <a href="/GrapeMind/logout.php" class="logout">Se déconnecter</a>
        <?php endif; ?>
    </div>
    <!-- Menu Déroulant -->
    <div class="menu-icon">
        <span id="menu-toggle">&#9776;</span>
    </div>
    <div id="dropdown-menu" class="dropdown-menu">
        <a href="/GrapeMind/profile_user.php">👤 Mon Profil</a>
        <a href="/GrapeMind/statistic.php">📊 Statistiques</a>
        <a href="/GrapeMind/components/user/user-settings.php">⚙️ Paramètres Du Compte</a>
    </div>
    <script src="js/menu_header.js"></script>
</header>
