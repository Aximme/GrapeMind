<?php
session_start();
include  __DIR__."/../header.php";
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Préférences</title>
    <link rel="stylesheet" href="style_quizz.css"> <!-- Lien vers le CSS -->
    <link rel="stylesheet" href="style_quizz.css"> <!-- Lien vers le CSS -->
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

<script src="script_quizz.js"></script> <!-- On met le JS dans un fichier à part -->
</body>
</html>
