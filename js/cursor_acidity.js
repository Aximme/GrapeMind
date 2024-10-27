document.addEventListener("DOMContentLoaded", function() {
    // ---- Code pour le degré d'alcool ----
    var abvElement = document.querySelector('.b');
    var containerElement = document.querySelector('.container1');
    var cursorElement = document.querySelector('.group-item');

    if (abvElement && containerElement && cursorElement) {
        var abvValue = parseFloat(abvElement.textContent);
        if (!isNaN(abvValue)) {
            var containerWidth = containerElement.clientWidth;
            var maxABV = 20; // Vous pouvez ajuster ce maximum selon vos données
            var percentage = Math.min(Math.max(abvValue / maxABV, 0), 1);
            var cursorPosition = percentage * containerWidth;
            cursorElement.style.left = (cursorPosition - (cursorElement.clientWidth / 2)) + 'px';
        }
    }

    // ---- Code pour l'acidité ----
    var acidityElement = document.querySelector('.title');
    var acidityContainer = document.querySelector('.container');
    var acidityCursor = document.querySelector('.group-child');
    var tooltipElement = document.querySelector('.tooltip');

    if (acidityElement && acidityContainer && acidityCursor && tooltipElement) {
        var acidityValue = acidityElement.textContent.trim().toLowerCase();

        // Déterminer la position du curseur en fonction de l'acidité (low, medium, high)
        var acidityPercentage = 0;
        switch (acidityValue) {
            case 'low':
                acidityPercentage = 0.25;
                break;
            case 'medium':
                acidityPercentage = 0.5;
                break;
            case 'high':
                acidityPercentage = 0.75;
                break;
            default:
                acidityPercentage = 0.5;
        }

        // Ajuster la position du curseur pour l'acidité
        var acidityContainerWidth = acidityContainer.clientWidth;
        var acidityCursorPosition = acidityPercentage * acidityContainerWidth;

        // Ajuster la position du curseur pour l'acidité
        acidityCursor.style.left = (acidityCursorPosition - (acidityCursor.clientWidth / 2)) + 'px';

        // Ajuster la position de l'élément `.tooltip` pour suivre le curseur
        tooltipElement.style.position = 'absolute';
        tooltipElement.style.left = (acidityCursorPosition - (tooltipElement.clientWidth / 2)) + 'px';
    }
});
