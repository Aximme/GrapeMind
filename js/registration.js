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
            case "username":
            case "address":
                if (!value.trim()) {
                    error = "Ce champ ne peut pas être vide.";
                }
                break;
            case "email":
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                    error = "Adresse e-mail invalide.";
                }
                break;
            case "password":
                if (!/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>]).{8,}$/.test(value)) {
                    error = "Le mot de passe doit contenir au moins 8 caractères, un chiffre et un caractère spécial.";
                }
                break;
            case "confirm_password":
                if (value !== $("#password").val()) {
                    error = "Les mots de passe ne correspondent pas.";
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

    $("#registration-form").on("submit", function (e) {
        e.preventDefault();

        const formData = {
            username: $("#username").val(),
            email: $("#email").val(),
            address: $("#address").val(),
            password: $("#password").val(),
            confirm_password: $("#confirm_password").val(),
        };

        $.ajax({
            url: "user_registration.php",
            type: "POST",
            data: formData,
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    window.location.href = "index.php";
                } else {
                    alert("Erreur lors de la création du compte.");
                }
            },
            error: function (xhr, status, error) {
                console.log("Erreur AJAX : ", status, error, xhr.responseText);
                alert("Erreur serveur lors de l'inscription.");
            },
        });
    });
});
