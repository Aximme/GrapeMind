document.addEventListener("DOMContentLoaded", function () {
    const map = L.map("map").setView([46.603354, 1.888334], 6);

    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    const markers = L.markerClusterGroup();

    function loadWineries() {
        const bounds = map.getBounds();
        const northEast = bounds.getNorthEast();
        const southWest = bounds.getSouthWest();

        fetch(`/GrapeMind/components/wine_map/get_winery_coordinates.php?ne_lat=${northEast.lat}&ne_lng=${northEast.lng}&sw_lat=${southWest.lat}&sw_lng=${southWest.lng}`)
            .then(response => response.json())
            .then(wineries => {
                markers.clearLayers();

                wineries.forEach(winery => {
                    if (winery.winery_lat && winery.winery_lon) {
                        const marker = L.marker([winery.winery_lat, winery.winery_lon]);
                        const popupContent = `
                            <strong>${winery.WineryName}</strong><br>
                            ${winery.Website ? `<a href="${winery.Website}" target="_blank">Visiter le site du domaine</a>` : `<a href="https://www.google.com/search?q=${encodeURIComponent(winery.WineryName)}" target="_blank">Site du domaine indisponible, recherche sur google</a>`}
                        `;
                        marker.bindPopup(popupContent);
                        markers.addLayer(marker);
                    }
                });

                map.addLayer(markers);
            })
            .catch(error => console.error("Error loading wineries:", error));
    }

    map.on('moveend', loadWineries);

    const regionSelect = document.getElementById("region-select");
    const regionDetails = document.getElementById("region-details");

    fetch("/GrapeMind/components/wine_map/regions.json")
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
                            <h2>🇫🇷 Aucune région sélectionnée</h2>
                            <p>🗺️ Sélectionnez une région dans le menu déroulant pour voir ses détails.</p>
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
                `;
            });
        })
        .catch(error => {
            console.error("Error loading regions:", error);
        });
    loadWineries();
});