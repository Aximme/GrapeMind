<?php
session_start();
if (!isset($_SESSION['user'])) {
    // Redirection vers la page de connexion si l'utilisateur n'est pas connecté
    header('Location: /GrapeMind/login.php');
    exit;
}
include __DIR__ . '/../header.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Préférences</title>
    <link rel="stylesheet" href="style_quizz.css">
</head>
<body>

<div class="radio-input" id="quiz-container">
    <div class="progress-bar-container">
        <div class="progress-bar" id="progress-bar"></div>
    </div>

    <div class="info">
        <span id="question-text" class="question"></span>
        <span id="steps" class="steps"></span>
    </div>

    <div id="answers-container" class="fade-in slide-in"></div>

    <button id="next-btn" class="next-button" disabled>Suivant</button>
</div>

<script src="quiz-loader.js"></script>
</body>
</html>
