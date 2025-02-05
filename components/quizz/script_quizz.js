document.addEventListener("DOMContentLoaded", function () {
    const quizData = [
        { question: "Quelle couleur de vin préférez-vous ?", options: ["Rouge", "Blanc", "Rosé", "Je ne bois pas d'alcool"] },
        { question: "Quels goûts aimez-vous ?", options: ["Sucré", "Salé", "Acide", "Amer", "Umami"] },
        { question: "Quels fruits préférez-vous ?", options: ["Fraise", "Myrtille", "Framboise", "Cerise", "Mangue"] },
        { question: "Entrez un mot qui vous décrit le mieux :", input: true } // Question avec champ texte
    ];

    let currentIndex = 0;
    let userPreferences = [];

    function loadQuestion(index) {
        const quizContainer = document.getElementById("quiz-container");
        quizContainer.classList.add("fade-out");

        setTimeout(() => {
            if (index >= quizData.length) {
                showResults();
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
                let inputWrapper = document.createElement("div");
                inputWrapper.style.width = "100%";
                inputWrapper.style.display = "flex";
                inputWrapper.style.justifyContent = "center";
                inputWrapper.style.marginTop = "10px";

                let inputField = document.createElement("input");
                inputField.type = "text";
                inputField.name = "text";
                inputField.classList.add("input");
                inputField.placeholder = "Tapez ici...";
                inputField.required = true;
                inputField.setAttribute("autocomplete", "off");
                inputField.setAttribute("spellcheck", "false");
                inputField.style.width = "80%";
                inputField.style.padding = "12px";
                inputField.style.fontSize = "16px";
                inputField.style.border = "2px solid #ccc";
                inputField.style.borderRadius = "5px";
                inputField.style.backgroundColor = "#fff";
                inputField.style.textAlign = "center";
                inputField.style.display = "block";
                inputField.style.visibility = "visible";
                inputField.addEventListener("input", checkUserInput);
                inputField.addEventListener("focus", () => {
                    inputField.style.borderColor = "#7b1c13";
                });
                inputField.addEventListener("blur", () => {
                    inputField.style.borderColor = "#ccc";
                });

                inputWrapper.appendChild(inputField);
                answersContainer.appendChild(inputWrapper);
                setTimeout(() => inputField.focus(), 100);
            } else {
                question.options.forEach((option, i) => {
                    let id = `value-${index}-${i}`;

                    let input = document.createElement("input");
                    input.type = "checkbox";
                    input.id = id;
                    input.name = "value-checkbox";
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
        if (userInput) {
            document.getElementById("next-btn").disabled = userInput.value.trim().length === 0;
        }
    }

    function saveSelection() {
        let selectedOptions = [];
        const currentQuestion = quizData[currentIndex];

        if (currentQuestion.input) {
            let userInput = document.querySelector(".input").value.trim();
            if (userInput) {
                selectedOptions.push(userInput);
            }
        } else {
            document.querySelectorAll("#answers-container input:checked").forEach(input => {
                selectedOptions.push(input.value);
            });
        }

        userPreferences.push({
            question: currentQuestion.question,
            answers: selectedOptions
        });

        currentIndex++;
        loadQuestion(currentIndex);
    }

    function showResults() {
        document.getElementById("quiz-container").innerHTML = `
            <p class="question">Merci pour vos réponses ! Voici vos préférences :</p>
            <div class="result-container fade-in">
                ${userPreferences.map(pref => `
                    <p><strong>${pref.question}</strong></p>
                    <p>${pref.answers.join(", ")}</p>
                `).join("")}
            </div>
            <button class="restart-button">Recommencer</button>
        `;

        document.querySelector(".restart-button").addEventListener("click", () => {
            location.reload();
        });
    }

    document.getElementById("next-btn").addEventListener("click", saveSelection);

    loadQuestion(currentIndex);
});
