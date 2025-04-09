const WINE_TYPE_TRANSLATIONS = {
    "Red": "Rouge",
    "White": "Blanc",
    "Rosé": "Rosé",
    "Sparkling": "Effervescent",
};

document.addEventListener("DOMContentLoaded", function () {
    const map = L.map("map").setView([46.603354, 1.888334], 6);

    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        maxZoom: 19,
        attribution: "© OpenStreetMap contributors"
    }).addTo(map);

    const markers = L.markerClusterGroup();
    const regionSelect = document.getElementById("region-select");
    const regionDetails = document.getElementById("region-details");

    function loadWineries() {
        fetch("/components/wine_map/get_winery_coordinates.php")
            .then(response => response.json())
            .then(wineries => {
                markers.clearLayers();

                wineries.forEach(winery => {
                    if (winery.winery_lat && winery.winery_lon) {
                        const marker = L.marker([winery.winery_lat, winery.winery_lon]);
                        marker.on("click", () => loadWines(winery.WineryID));
                        markers.addLayer(marker);
                    }
                });

                map.addLayer(markers);
            })
            .catch(error => console.error("Erreur de chargement des domaines :", error));
    }

    function loadRegions() {
        fetch("/components/wine_map/regions.json")
            .then(response => response.json())
            .then(regions => {
                regions.forEach((region, index) => {
                    const option = document.createElement("option");
                    option.value = index;
                    option.textContent = region.name;
                    regionSelect.appendChild(option);
                });

                regionSelect.addEventListener("change", function () {
                    const selectedIndex = this.value;
                    if (selectedIndex === "") {
                        regionDetails.innerHTML = `
                            <div class="info-card">
                                <h2>Aucune région sélectionnée</h2>
                                <p>Sélectionnez une région dans le menu déroulant pour voir ses détails.</p>
                            </div>
                        `;
                        map.setView([46.603354, 1.888334], 6);
                        return;
                    }

                    const selectedRegion = regions[selectedIndex];
                    map.setView(selectedRegion.coords, 7);
                    regionDetails.innerHTML = `
                        <div class="info-card">
                            <img src="${selectedRegion.image}" alt="${selectedRegion.name}">
                            <h2>${selectedRegion.name}</h2>
                            <p>${selectedRegion.description}</p>
                        </div>
                        <div id="wine-list"></div>
                    `;
                });
            })
            .catch(error => console.error("Erreur de chargement des régions :", error));
    }

    function selectSuggestion(vinId) {
        fetch("/components/wine/set_vin_id.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `vin_id=${encodeURIComponent(vinId)}`
        })
            .then(() => window.location.href = "/components/wine/wine-details.php")
            .catch(error => console.error("Erreur lors de l'envoi de l'ID :", error));
    }

    window.loadWines = function (wineryID) {
        fetch(`/components/wine_map/get_wines_by_winery.php?winery_id=${wineryID}`)
            .then(response => response.json())
            .then(wines => {
                let wineSection = document.getElementById("wine-list");
                if (!wineSection) {
                    wineSection = document.createElement("div");
                    wineSection.id = "wine-list";
                    regionDetails.appendChild(wineSection);
                }

                wineSection.innerHTML = "<h2>Vins disponibles</h2>";

                if (!Array.isArray(wines) || wines.length === 0) {
                    wineSection.innerHTML += "<p>Aucun vin trouvé pour ce domaine.</p>";
                    return;
                }

                wines.forEach(wine => {
                    let formattedName = wine.NameWine_WithWinery.replace(/(\w)([A-Z])/g, "$1 $2");
                    let formattedPrice = wine.Price ? `${parseFloat(wine.Price).toFixed(2).replace(".", ",")}€` : "Prix non disponible";
                    let wineType = WINE_TYPE_TRANSLATIONS[wine.Type] || wine.Type;

                    let wineCard = document.createElement("div");
                    wineCard.className = "wine-card";
                    wineCard.style.cursor = "pointer";
                    wineCard.innerHTML = `
                        <img src="${wine.thumb}" alt="${formattedName}" class="wine-img">
                        <div class="wine-info">
                            <h3>${formattedName}</h3>
                            <p><strong>Type :</strong> ${wineType}</p>
                            <p><strong>Prix :</strong> ${formattedPrice}</p>
                        </div>
                    `;

                    wineCard.addEventListener("click", () => selectSuggestion(wine.idwine));

                    wineSection.appendChild(wineCard);
                });
            })
            .catch(error => console.error("Erreur AJAX :", error));
    };

    loadWineries();
    loadRegions();
});