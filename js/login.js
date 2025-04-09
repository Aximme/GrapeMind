$(document).ready(function () {
    let fieldsTouched = {};

    $("input").on("blur", function () {
        const id = $(this).attr("id");
        fieldsTouched[id] = true;
        validateAndStyleField(id);
        toggleSubmitButton();
    });

    $("input").on("input", function () {
        const id = $(this).attr("id");
        if (fieldsTouched[id]) {
            validateAndStyleField(id);
            toggleSubmitButton();
        }
    });

    function validateAndStyleField(id) {
        const value = $("#" + id).val();
        const error = validateField(id, value);

        const $field = $("#" + id);
        const $errorMessage = $("#" + id + "-error");

        if (error) {
            $errorMessage.text(error).css("color", "red");
            $field.removeClass("valid").addClass("invalid").css({
                "border-color": "red",
                "background-color": "#ffe6e6",
            });
        } else {
            $errorMessage.text("");
            $field.removeClass("invalid").addClass("valid").css({
                "border-color": "green",
                "background-color": "#e6ffe6",
            });
        }
    }

    function validateField(id, value) {
        let error = "";
        switch (id) {
            case "email":
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                    error = "Adresse e-mail invalide.";
                }
                break;
            case "password":
                if (!value.trim()) {
                    error = "Le mot de passe ne peut pas être vide.";
                }
                break;
        }
        return error;
    }

    function toggleSubmitButton() {
        let allValid = true;

        $("input").each(function () {
            const id = $(this).attr("id");
            const value = $(this).val();
            const error = validateField(id, value);

            if (error || !value.trim()) {
                allValid = false;
            }
        });

        if (allValid) {
            $("button").removeAttr("disabled");
        } else {
            $("button").attr("disabled", true);
        }
    }

    $("#login-form").on("submit", function (e) {
        e.preventDefault();

        const formData = {
            email: $("#email").val(),
            password: $("#password").val(),
        };

        $.ajax({
            url: "user_login.php",
            type: "POST",
            data: formData,
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    window.location.href = "index.php";
                } else {
                    alert("Erreur lors de la connexion.");
                }
            },
            error: function () {
                alert("Erreur serveur lors de la tentative de connexion.");
            },
        });
    });
});
