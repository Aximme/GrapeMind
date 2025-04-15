function fetchSuggestions(query) {
    const suggestionsContainer = document.getElementById("search-suggestions");

    if (query.length < 2) {
        suggestionsContainer.innerHTML = "";
        suggestionsContainer.style.display = 'none';
        return;
    }

    suggestionsContainer.style.display = 'block';

    fetch(`/components/wine_map/search_bar_server.php?query=${encodeURIComponent(query)}`)
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
    fetch("/components/wine/set_vin_id.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: `vin_id=${vinId}`
    })
        .then(response => response.text())
        .then(data => {
            console.log(data);
            window.location.href = "/components/wine/wine-details.php";
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
                window.location.href = `/components/wine_map/search_results.php?query=${encodeURIComponent(query)}`;
            }
        });
    }
});
