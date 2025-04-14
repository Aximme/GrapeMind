/*
    Filtrage des résultats de vin par prix & couleur sur l'index.

    Contenu :
    - Initialise un noUiSlider pour sélectionner une plage de prix.
    - Récupère les couleurs sélectionnées via les inputs.
    - Construit l’URL de redirection avec les filtres appliqués.

    Utilisation :
    - Appelé sur la page de recherche avancée.
    - Nécessite l’élément #price-slider et le bouton .search-button_price.
*/

document.addEventListener('DOMContentLoaded', function () {
    const slider = document.getElementById('price-slider');
    const searchButton = document.querySelector('.search-button_price');

    noUiSlider.create(slider, {
        start: [16, 500],
        connect: true,
        range: {
            'min': 0,
            'max': 1000
        },
        step: 1,
        tooltips: [true, true],
        format: {
            to: (value) => Math.round(value) + '€',
            from: (value) => Number(value.replace('€', ''))
        }
    });


    function getFilterValues() {

        const wineColors = Array.from(document.querySelectorAll('.wine-options input:checked'))
            .map(input => input.value); // Collecte les valeurs des couleurs sélectionnées


        const priceRange = slider.noUiSlider.get().map(price => Number(price.replace('€', '')));

        return {
            wineColors,
            minPrice: priceRange[0],
            maxPrice: priceRange[1]
        };
    }

    function applyFilters() {
        const filters = getFilterValues();
        const query = new URLSearchParams();

        filters.wineColors.forEach(color => query.append('wineColor[]', color));

        query.append('minPrice', filters.minPrice);
        query.append('maxPrice', filters.maxPrice);

        window.location.href = '/GrapeMind/components/search_results.php?' + query.toString();
    }

    searchButton.addEventListener('click', applyFilters);
});
