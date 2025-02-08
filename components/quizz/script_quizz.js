document.addEventListener("DOMContentLoaded", function () {
    const quizData = [
        { id: 1, question: "Quelle couleur de vin préférez-vous ?", options: ["Rouge", "Blanc", "Rosé", "Je ne bois pas d'alcool"] },
        { id: 2, question: "Quels goûts aimez-vous ?", options: ["Sucré", "Salé", "Acide", "Amer", "Umami"] },
        { id: 3, question: "Quels fruits préférez-vous ?", options: ["Fraise", "Myrtille", "Framboise", "Cerise", "Mangue"] },
        { id: 4, question: "Entrez un mot qui vous décrit le mieux :", input: true }
    ];

    let currentIndex = 0;
    let userPreferences = [];

    function loadQuestion(index) {
        const quizContainer = document.getElementById("quiz-container");
        quizContainer.classList.add("fade-out");

        setTimeout(() => {
            if (index >= quizData.length) {
                sendResponses();
                return;
            }

            quizContainer.classList.remove("fade-out");
            quizContainer.classList.add("fade-in");

            const question = quizData[index];
            document.getElementById("question-text").innerText = question.question;
            document.getElementById("steps").innerText = `${index + 1}/${quizData.length}`;
            document.getElementById("progress-bar").style.width = `${((index + 1) / quizData.length) * 100}%`;

            const answersContainer = document.getElementById("answers-container");
            answersContainer.innerHTML = "";

            if (question.input) {
                // Style spécifique pour la question avec input
                answersContainer.style.cssText = `
                    position: relative;
                    width: 100%;
                    min-height: 100px;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    margin: 20px 0;
                `;

                const inputField = document.createElement("input");
                inputField.type = "text";
                inputField.classList.add("input");
                inputField.placeholder = "Tapez ici...";
                inputField.style.cssText = `
                    display: block;
                    width: 80%;
                    max-width: 400px;
                    padding: 12px;
                    margin: 20px auto;
                    font-size: 16px;
                    border: 2px solid #ccc;
                    border-radius: 5px;
                    background-color: #fff;
                    text-align: center;
                    position: static;
                `;

                inputField.addEventListener("input", checkUserInput);
                answersContainer.appendChild(inputField);
                setTimeout(() => inputField.focus(), 100);
            } else {
                // Retour au style original pour les questions à choix multiples
                answersContainer.style.cssText = ''; // Réinitialiser les styles

                question.options.forEach((option, i) => {
                    let id = `value-${index}-${i}`;

                    let input = document.createElement("input");
                    input.type = "checkbox";
                    input.id = id;
                    input.value = option;

                    let label = document.createElement("label");
                    label.htmlFor = id;
                    label.innerText = option;

                    input.addEventListener("change", checkSelection);

                    answersContainer.appendChild(input);
                    answersContainer.appendChild(label);
                });
            }

            document.getElementById("next-btn").disabled = true;
        }, 300);
    }

    function checkSelection() {
        let checkedOptions = document.querySelectorAll("#answers-container input:checked");
        document.getElementById("next-btn").disabled = checkedOptions.length === 0;
    }

    function checkUserInput() {
        let userInput = document.querySelector(".input");
        document.getElementById("next-btn").disabled = userInput.value.trim().length === 0;
    }

    function saveSelection() {
        let selectedOptions = [];
        const currentQuestion = quizData[currentIndex];

        if (currentQuestion.input) {
            let userInput = document.querySelector(".input").value.trim();
            if (userInput) selectedOptions.push(userInput);
        } else {
            document.querySelectorAll("#answers-container input:checked").forEach(input => {
                selectedOptions.push(input.value);
            });
        }

        userPreferences.push({
            question_id: currentQuestion.id,
            answers: selectedOptions
        });

        currentIndex++;
        loadQuestion(currentIndex);
    }

    function sendResponses() {
        document.getElementById("quiz-container").innerHTML = "<p>Envoi en cours...</p>";

        fetch("save_quiz.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ responses: userPreferences })
        })
            .then(response => response.json())
            .then(data => {
                document.getElementById("quiz-container").innerHTML = `<p>${data.message || data.error}</p>`;
            })
            .catch(error => {
                console.error("Erreur :", error);
                document.getElementById("quiz-container").innerHTML = "<p>Une erreur est survenue. Veuillez réessayer.</p>";
            });
    }

    document.getElementById("next-btn").addEventListener("click", function () {
        this.disabled = true;
        saveSelection();
        setTimeout(() => { this.disabled = false; }, 500);
    });

    loadQuestion(currentIndex);
});