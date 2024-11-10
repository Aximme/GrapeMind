document.addEventListener('DOMContentLoaded', function () {
    const slider = document.getElementById('price-slider');
    const searchButton = document.querySelector('.search-button_price');

    // Initialisation de noUiSlider
    noUiSlider.create(slider, {
        start: [16, 500],
        connect: true,
        range: {
            'min': 10,
            'max': 500
        },
        step: 1,
        tooltips: [true, true],
        format: {
            to: (value) => Math.round(value) + '€',
            from: (value) => Number(value.replace('€', ''))
        }
    });

    // Fonction pour récupérer les valeurs des filtres
    function getFilterValues() {
        // Récupérer les valeurs sélectionnées des cases à cocher pour les types de vin
        const wineTypes = Array.from(document.querySelectorAll('.wine-options input:checked'))
            .map(input => input.value); // Utilise `value` au lieu de texte

        // Valeur de la plage de prix
        const priceRange = slider.noUiSlider.get().map(price => Number(price.replace('€', '')));

        return {
            wineTypes,
            priceRange
        };
    }



    // Fonction pour appliquer les filtres et rediriger
    function applyFilters() {
        const filters = getFilterValues();
        const query = new URLSearchParams();

        // Ajouter les types de vin sélectionnés
        filters.wineTypes.forEach(type => query.append('wineTypes[]', type));

        // Ajouter la plage de prix
        query.append('minPrice', filters.priceRange[0]);
        query.append('maxPrice', filters.priceRange[1]);

        // Rediriger vers la page de résultats avec les paramètres de filtre
        window.location.href = '/GrapeMind/components/results_filter.php?' + query.toString();
    }

    // Associer l'événement de clic au bouton pour lancer `applyFilters`
    searchButton.addEventListener('click', applyFilters);
});
