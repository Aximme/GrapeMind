function fetchSuggestions(query) {
    if (query.length < 2) {
        document.getElementById("search-suggestions").innerHTML = "";
        return;
    }

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
    // Envoi de l'ID sélectionné à `set_vin_id.php` pour le stocker en session
    fetch("/components/wine/set_vin_id.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: `vin_id=${vinId}`
    })
        .then(response => response.text())
        .then(data => {
            console.log(data); // Affiche la réponse pour vérifier que l'ID est stocké
            // Rediriger vers wine-details.php sans ID dans l'URL
            window.location.href = "/components/wine/wine-details.php";
        })
        .catch(error => console.error("Erreur lors de l'envoi de l'ID :", error));
}


