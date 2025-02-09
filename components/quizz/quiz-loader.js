document.addEventListener("DOMContentLoaded", async function () {
    let quizData = await fetchQuestions();
    let currentIndex = 0;
    let userPreferences = [];

    async function fetchQuestions() {
        try {
            const response = await fetch("save_quiz.php"); // Remplace par l'URL de ton fichier PHP
            if (!response.ok) throw new Error("Erreur lors de la récupération des questions.");
            return await response.json();
        } catch (error) {
            console.error("Erreur :", error);
            document.getElementById("quiz-container").innerHTML = "<p>Impossible de charger les questions. Veuillez réessayer plus tard.</p>";
            return [];
        }
    }

    function loadQuestion(index) {
        if (index >= quizData.length) {
            sendResponses();
            return;
        }

        const quizContainer = document.getElementById("quiz-container");
        quizContainer.classList.add("fade-out");

        setTimeout(() => {
            quizContainer.classList.remove("fade-out");
            quizContainer.classList.add("fade-in");

            const question = quizData[index];
            document.getElementById("question-text").innerText = question.question;
            document.getElementById("steps").innerText = `${index + 1}/${quizData.length}`;
            document.getElementById("progress-bar").style.width = `${((index + 1) / quizData.length) * 100}%`;

            const answersContainer = document.getElementById("answers-container");
            answersContainer.innerHTML = "";

            if (question.type === "input") {
                const textareaField = document.createElement("textarea");
                textareaField.classList.add("input");
                textareaField.placeholder = "Tapez votre réponse ici...";
                textareaField.rows = 4; // Nombre de lignes visibles
                textareaField.addEventListener("input", checkUserInput);
                answersContainer.appendChild(textareaField);
            } else if (question.type === "multiple") {
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

        if (currentQuestion.type === "input") {
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

    // Charger la première question
    if (quizData.length > 0) {
        loadQuestion(currentIndex);
    } else {
        document.getElementById("quiz-container").innerHTML = "<p>Aucune question trouvée.</p>";
    }
});
