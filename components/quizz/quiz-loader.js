document.addEventListener("DOMContentLoaded", async function () {
    let quizData = await fetchQuestions();
    let currentIndex = 0;
    let userPreferences = [];

    async function fetchQuestions() {
        try {
            const response = await fetch("quiz_loader.php");
            if (!response.ok) throw new Error("Erreur lors de la récupération des questions.");
            return await response.json();
        } catch (error) {
            console.error("Erreur lors du chargement des questions :", error);
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
                textareaField.rows = 4;
                textareaField.addEventListener("input", checkUserInput);
                answersContainer.appendChild(textareaField);
            } else if (question.type === "multiple") {
                question.options.forEach((option, i) => {
                    let id = `value-${index}-${i}`;
                    let input = document.createElement("input");
                    input.type = (index === 0 || index === quizData.length - 1) ? "checkbox" : "radio";
                    input.name = `question-${index}`;
                    input.id = id;
                    input.value = option;

                    let label = document.createElement("label");
                    label.htmlFor = id;
                    label.innerText = option;

                    input.addEventListener("change", checkSelection);

                    answersContainer.appendChild(input);
                    answersContainer.appendChild(label);
                });

                // Ajouter "Ne sais pas" sauf pour la première et la dernière question
                if (index !== 0 && index !== quizData.length - 1) {
                    let dontKnowId = `value-${index}-dontknow`;
                    let dontKnowInput = document.createElement("input");
                    dontKnowInput.type = "radio";
                    dontKnowInput.name = `question-${index}`;
                    dontKnowInput.id = dontKnowId;
                    dontKnowInput.value = "Ne sais pas";

                    let dontKnowLabel = document.createElement("label");
                    dontKnowLabel.htmlFor = dontKnowId;
                    dontKnowLabel.innerText = "Ne sais pas";

                    dontKnowInput.addEventListener("change", checkSelection);

                    answersContainer.appendChild(dontKnowInput);
                    answersContainer.appendChild(dontKnowLabel);
                }
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
        document.getElementById("quiz-container").innerHTML = `
            <p>Merci pour vos réponses</p>
            <button id="home-btn" class="next-button">Retour à l'accueil</button>
        `;

        document.getElementById("home-btn").addEventListener("click", function () {
            window.location.href = "/GrapeMind/index.php";
        });

        fetch("save_quiz.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ responses: userPreferences })
        })
            .then(response => response.text())
            .then(data => {
                try {
                    JSON.parse(data);
                } catch (error) {
                    console.error("Erreur JSON :", error);
                    document.getElementById("quiz-container").innerHTML = `<p>Erreur de réponse du serveur : ${data}</p>`;
                }
            });
    }

    document.getElementById("next-btn").addEventListener("click", function () {
        this.disabled = true;
        saveSelection();
        setTimeout(() => { this.disabled = false; }, 500);
    });

    if (quizData.length > 0) {
        loadQuestion(currentIndex);
    } else {
        document.getElementById("quiz-container").innerHTML = "<p>Aucune question trouvée.</p>";
    }
});
