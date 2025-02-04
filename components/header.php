<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GrapeMind</title>
    <link rel="stylesheet" href="/GrapeMind/css/main.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
        <div class="container-fluid">
            <!-- Logo -->
            <a class="navbar-brand d-flex align-items-center" href="/GrapeMind/index.php">
                <img src="/GrapeMind/assets/images/image_header.png" alt="GrapeMind Logo" height="40">
                <span class="ms-2 fw-bold">GrapeMind</span>
            </a>

            <!-- Bouton de menu responsive -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menu de navigation -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link fw-bold" href="/GrapeMind/index.php">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link fw-bold" href="/GrapeMind/components/wine_map/map-main.php">Carte</a></li>
                    <li class="nav-item"><a class="nav-link fw-bold" href="/GrapeMind/events.php">Événements</a></li>
                    <li class="nav-item"><a class="nav-link fw-bold" href="/GrapeMind/about.php">À propos</a></li>
                </ul>

                <!-- Authentification + Menu déroulant du profil -->
                <div class="d-flex align-items-center">
                    <?php if (!isset($_SESSION['user'])): ?>
                        <a href="/GrapeMind/login.php" class="btn btn-outline-dark me-2">Se connecter</a>
                        <a href="/GrapeMind/registration.php" class="btn btn-light border border-danger text-danger">S'inscrire</a>
                    <?php else: ?>
                        <div class="dropdown">
                            <button class="btn btn-outline-dark dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                👤 Mon Profil
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                                <li><a class="dropdown-item" href="/GrapeMind/profile_user.php">👤 Mon Profil</a></li>
                                <li><a class="dropdown-item" href="/GrapeMind/statistic.php">📊 Statistiques</a></li>
                                <li><a class="dropdown-item" href="/GrapeMind/components/user/user-settings.php">⚙️ Paramètres</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="/GrapeMind/logout.php">🚪 Se déconnecter</a></li>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
</header>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
