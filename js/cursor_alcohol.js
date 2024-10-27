document.addEventListener("DOMContentLoaded", function() {
    // Récupérer la valeur du degré d'alcool à partir de l'élément existant dans la page
    var abvElement = document.querySelector('.b');
    var containerElement = document.querySelector('.container1');
    var cursorElement = document.querySelector('.group-item');

    if (abvElement && containerElement && cursorElement) {
        // Récupérer la valeur ABV depuis le contenu du texte
        var abvValue = parseFloat(abvElement.textContent);

        // Vérifier si la valeur ABV est valide
        if (!isNaN(abvValue)) {
            // La largeur de la barre (container1)
            var containerWidth = containerElement.clientWidth;

            // Calculer la position du curseur en pourcentage
            // Supposons que le maximum de degré d'alcool est 20 pour le calcul (ajustez selon vos besoins)
            var maxABV = 20;
            var percentage = Math.min(Math.max(abvValue / maxABV, 0), 1);

            // Calculer la position finale du curseur en pixels
            var cursorPosition = percentage * containerWidth;

            // Ajuster la position du curseur
            cursorElement.style.left = (cursorPosition - (cursorElement.clientWidth / 2)) + 'px';
        }
    }
});
