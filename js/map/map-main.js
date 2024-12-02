document.addEventListener("DOMContentLoaded", function () {
    const map = L.map("map").setView([46.603354, 1.888334], 6); // Centré sur la France

    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    const regionSelect = document.getElementById("region-select");
    const regionDetails = document.getElementById("region-details");

    // Charger les données des régions depuis le fichier JSON
    fetch("/GrapeMind/components/wine_map/regions.json")
        .then(response => response.json())
        .then(regions => {
            // Ajouter les régions au menu déroulant
            regions.forEach((region, index) => {
                const option = document.createElement("option");
                option.value = index;
                option.textContent = region.name;
                regionSelect.appendChild(option);
            });

            // Gérer la sélection d'une région
            regionSelect.addEventListener("change", function () {
                const selectedIndex = this.value;
                if (selectedIndex === "") {
                    regionDetails.innerHTML = `
                        <div class="info-card">
                            <h2>Aucune région sélectionnée</h2>
                            <p>Sélectionnez une région dans le menu déroulant pour voir ses détails.</p>
                        </div>
                    `;
                    map.setView([46.603354, 1.888334], 6); // Recentrer sur la France
                    return;
                }

                const selectedRegion = regions[selectedIndex];
                map.setView(selectedRegion.coords, 7); // Zoomer sur la région
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
            console.error("Erreur lors du chargement des régions :", error);
        });
});