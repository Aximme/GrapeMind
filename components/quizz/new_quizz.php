<?php
global $conn;
include __DIR__ . '/../header.php';

// Définition des questions avec les images
$questions = [
    ["question" => "Aimez-vous le goût des fruits rouges ?", "image" => "fruits_rouges.png"],
    ["question" => "Préférez-vous le chocolat au lait ou noir ?", "image" => "chocolat.png"],
    ["question" => "Aimez-vous les plats épicés ?", "image" => "epices.png"]
];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="globals.css" />
    <link rel="stylesheet" href="styleguide.css" />
    <link rel="stylesheet" href="style.css" />
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let questions = <?php echo json_encode($questions); ?>;
            let currentIndex = 0;

            function loadQuestion(index) {
                if (index >= questions.length) {
                    document.getElementById("quiz-container").innerHTML = "<p class='div'>Quiz terminé ! Merci pour votre participation.</p>";
                    return;
                }

                document.getElementById("question-text").innerText = questions[index].question;
                document.getElementById("question-image").src = questions[index].image;
                document.getElementById("progress-text").innerText = `Question ${index + 1}/${questions.length}`;
            }

            document.querySelectorAll(".answer-btn").forEach(button => {
                button.addEventListener("click", function () {
                    currentIndex++;
                    loadQuestion(currentIndex);
                });
            });

            loadQuestion(currentIndex);
        });
    </script>
</head>
<body>
<div class="QUIZZ">
    <div id="quiz-container" class="QUIZZ-q">
        <div class="depth-frame">
            <div class="div-wrapper">
                <p id="question-text" class="div"></p>
            </div>
        </div>

        <!-- Boutons de réponse respectant tes couleurs et disposition -->
        <div class="depth-frame-wrapper">
            <button class="depth-frame-4 answer-btn"><span class="text-wrapper-2">Oui</span></button>
        </div>
        <div class="depth-frame-5">
            <button class="depth-frame-4 answer-btn"><span class="text-wrapper-3">Passer la Question</span></button>
        </div>
        <div class="depth-frame-6">
            <button class="depth-frame-4 answer-btn"><span class="text-wrapper-4">Non</span></button>
        </div>

        <!-- Progression -->
        <div class="depth-frame-7">
            <div class="depth-frame-8">
                <div class="depth-frame-9">
                    <div class="depth-frame-10">
                        <div id="progress-text" class="text-wrapper-5"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Image associée à la question -->
        <img id="question-image" class="temp-image-b-gwh-ib" src="" />
    </div>
</div>
</body>
</html>

<?php include __DIR__ . '/../footer.php'; ?>
