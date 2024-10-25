Dossier : `votreNom`

### index.php
```php
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire Nombre</title>
</head>
<body>
    <form action="votrePrenom.php" method="post">
        <label for="nombre">Entrez un nombre :</label>
        <input type="number" id="nombre" name="nombre" required>
        <button type="submit">Valider</button>
    </form>
</body>
</html>
```

### votrePrenom.php
```php
<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'])) {
    $_SESSION['nombre'] = $_POST['nombre'];
} else {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire Nom</title>
</head>
<body>
    <form action="voici.php" method="post">
        <label for="nom">Entrez votre nom :</label>
        <input type="text" id="nom" name="nom" required>
        <button type="submit">Valider</button>
    </form>
</body>
</html>
```

### voici.php
```php
<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nom'])) {
    $nom = htmlspecialchars($_POST['nom']);
    $nombre = isset($_SESSION['nombre']) ? htmlspecialchars($_SESSION['nombre']) : null;

    if ($nombre !== null) {
        $numero_etudiant = 123456; // Remplacez ce nombre par votre numéro étudiant
        $couleur = ($numero_etudiant % 2 === 0) ? 'red' : 'blue';
        echo "<p style=\"color: $couleur;\">Vous êtes $nom et vous avez $nombre ans</p>";
    } else {
        echo "<p>Erreur : nombre non défini.</p>";
    }
} else {
    header('Location: votrePrenom.php');
    exit();
}
?>
```

### Explications
1. **index.php** : Contient un formulaire demandant un nombre, utilisant la méthode POST. L'utilisateur est redirigé vers `votrePrenom.php` après validation.
2. **votrePrenom.php** : Enregistre le nombre dans une session et propose un second formulaire pour saisir le nom. L'utilisateur est redirigé vers `voici.php` après validation.
3. **voici.php** : Affiche le nombre et le nom fournis. La couleur de la phrase dépend de la parité du numéro étudiant.

### Cas supplémentaires

#### 1. Transmission par `GET` entre `votrePrenom.php` et `voici.php`

- **votrePrenom.php** :
```php
<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'])) {
    $nombre = $_POST['nombre'];
} else {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire Nom</title>
</head>
<body>
    <form action="voici.php" method="get">
        <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>">
        <label for="nom">Entrez votre nom :</label>
        <input type="text" id="nom" name="nom" required>
        <button type="submit">Valider</button>
    </form>
</body>
</html>
```

- **voici.php** :
```php
<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['nom']) && isset($_GET['nombre'])) {
    $nom = htmlspecialchars($_GET['nom']);
    $nombre = htmlspecialchars($_GET['nombre']);
    $numero_etudiant = 123456; // Remplacez ce nombre par votre numéro étudiant
    $couleur = ($numero_etudiant % 2 === 0) ? 'red' : 'blue';
    echo "<p style=\"color: $couleur;\">Vous êtes $nom et vous avez $nombre ans</p>";
} else {
    header('Location: votrePrenom.php');
    exit();
}
?>
```

#### 2. Transmission par champ caché entre `votrePrenom.php` et `voici.php`

- **votrePrenom.php** :
```php
<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'])) {
    $nombre = $_POST['nombre'];
} else {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire Nom</title>
</head>
<body>
    <form action="voici.php" method="post">
        <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>">
        <label for="nom">Entrez votre nom :</label>
        <input type="text" id="nom" name="nom" required>
        <button type="submit">Valider</button>
    </form>
</body>
</html>
```

- **voici.php** :
```php
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nom']) && isset($_POST['nombre'])) {
    $nom = htmlspecialchars($_POST['nom']);
    $nombre = htmlspecialchars($_POST['nombre']);
    $numero_etudiant = 123456; // Remplacez ce nombre par votre numéro étudiant
    $couleur = ($numero_etudiant % 2 === 0) ? 'red' : 'blue';
    echo "<p style=\"color: $couleur;\">Vous êtes $nom et vous avez $nombre ans</p>";
} else {
    header('Location: votrePrenom.php');
    exit();
}
?>
```

#### 3. Transmission par session avec destruction après lecture

- **votrePrenom.php** :
```php
<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'])) {
    $_SESSION['nombre'] = $_POST['nombre'];
} else {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire Nom</title>
</head>
<body>
    <form action="voici.php" method="post">
        <label for="nom">Entrez votre nom :</label>
        <input type="text" id="nom" name="nom" required>
        <button type="submit">Valider</button>
    </form>
</body>
</html>
```

- **voici.php** :
```php
<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nom'])) {
    $nom = htmlspecialchars($_POST['nom']);
    if (isset($_SESSION['nombre'])) {
        $nombre = htmlspecialchars($_SESSION['nombre']);
        unset($_SESSION['nombre']); // Destruction de la variable de session
        $numero_etudiant = 123456; // Remplacez ce nombre par votre numéro étudiant
        $couleur = ($numero_etudiant % 2 === 0) ? 'red' : 'blue';
        echo "<p style=\"color: $couleur;\">Vous êtes $nom et vous avez $nombre ans</p>";
    } else {
        echo "<p>Erreur : nombre non défini.</p>";
    }
} else {
    header('Location: votrePrenom.php');
    exit();
}
?>
```

### Cas supplémentaires SYNTHESE

#### 1. Transmission par `GET` entre `votrePrenom.php` et `voici.php`

- **votrePrenom.php** :
  - Remplacer `$_SESSION['nombre'] = $_POST['nombre'];` par `$nombre = $_POST['nombre'];`
  - Modifier la méthode du formulaire de `post` à `get`
  - Ajouter un champ caché `<input type="hidden" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>">`

- **voici.php** :
  - Modifier la méthode vérifiée de `POST` à `GET`
  - Remplacer `$_SESSION['nombre']` par `$_GET['nombre']`

#### 2. Transmission par champ caché entre `votrePrenom.php` et `voici.php`

- **votrePrenom.php** :
  - Remplacer `$_SESSION['nombre'] = $_POST['nombre'];` par `$nombre = $_POST['nombre'];`
  - Ajouter un champ caché `<input type="hidden" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>">`

- **voici.php** :
  - Remplacer `$_SESSION['nombre']` par `$_POST['nombre']`

#### 3. Transmission par session avec destruction après lecture

- **votrePrenom.php** :
  - Aucune modification nécessaire

- **voici.php** :
  - Ajouter `unset($_SESSION['nombre']);` après l'utilisation de `$_SESSION['nombre']` pour détruire la variable de session
