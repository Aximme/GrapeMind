/*
    Barre de recherche dynamique avec suggestions en live.

    Contenu :
    - Affiche des suggestions de vins en fonction des préférences de l’utilisateur (questionnaire).
    - Récupère les résultats via search_bar_server.php.
    - Envoie l’ID du vin sélectionné à set_vin_id.php.
    - Redirige vers la fiche vin ou la page de résultats selon l’action.

    Utilisation :
    - Appelé avec entrer ou clic sur l’icône de recherche.
    - Nécessite #search-suggestions et .search-bar.
*/

function fetchSuggestions(query) {
    const suggestionsContainer = document.getElementById("search-suggestions");

    if (query.length < 2) {
        suggestionsContainer.innerHTML = "";
        suggestionsContainer.style.display = 'none';
        return;
    }

    suggestionsContainer.style.display = 'block';

    fetch(`/GrapeMind/components/wine_map/search_bar_server.php?query=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            let suggestions = "";
            data.slice(0, 4).forEach(item => {
                suggestions += `
                    <div class='search-suggestion-item' onclick="selectSuggestion(${item.idwine})">
                        <img src="${item.thumb}" alt="${item.name}" style="width: 30px; height: 30px; margin-right: 10px;">
                        ${item.name}
                    </div>`;
            });
            document.getElementById("search-suggestions").innerHTML = suggestions;
        })
        .catch(error => console.error('Erreur lors de la récupération des suggestions:', error));
}

function selectSuggestion(vinId) {
    // Envoie l'ID du vin sélectionné à `set_vin_id.php` pour le stocker en session
    fetch("/GrapeMind/components/wine/set_vin_id.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: `vin_id=${vinId}`
    })
        .then(response => response.text())
        .then(data => {
            console.log(data);
            window.location.href = "/GrapeMind/components/wine/wine-details.php";
        })
        .catch(error => console.error("Erreur lors de l'envoi de l'ID :", error));
}

// Fonction pour rediriger vers la page de résultats lors du clic sur le bouton "Rechercher"
document.addEventListener('DOMContentLoaded', () => {
    const searchButton = document.getElementById('search-icon');
    if (searchButton) {
        searchButton.addEventListener('click', function(event) {
            event.preventDefault(); // Empêche le rechargement de la page
            const query = document.querySelector('.search-bar').value;
            if (query) {
                window.location.href = `/GrapeMind/components/wine_map/search_results.php?query=${encodeURIComponent(query)}`;
            }
        });
    }
});
