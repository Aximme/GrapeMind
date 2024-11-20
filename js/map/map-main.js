// Initialisation de la carte
am5.ready(function () {
    let root = am5.Root.new("map");

    root.setThemes([am5themes_Animated.new(root)]);

    let chart = root.container.children.push(
        am5map.MapChart.new(root, {
            projection: am5map.geoMercator()
        })
    );

// Modifier l'arrière-plan
    chart.seriesContainer.set("background", am5.Rectangle.new(root, {
        fill: am5.color("#ffffff") // Couleur blanche
    }));

    // Charger la carte des départements français
    let polygonSeries = chart.series.push(
        am5map.MapPolygonSeries.new(root, {
            geoJSON: am5geodata_franceDepartmentsLow
        })
    );

    // Configuration des régions
    polygonSeries.mapPolygons.template.setAll({
        interactive: true,
        tooltipText: "{name}",
        fill: am5.color(0x74b3ce)
    });

    polygonSeries.mapPolygons.template.states.create("hover", {
        fill: am5.color(0x003f5c)
    });

    // Zoom et affichage des infos
    polygonSeries.mapPolygons.template.events.on("click", function (ev) {
        const regionName = ev.target.dataItem.dataContext.name || "Région inconnue";

        // Convertir les coordonnées de la souris en coordonnées géographiques
        const geoPoint = chart.invert({ x: ev.point.x, y: ev.point.y });

        if (geoPoint) {
            // Zoomer sur l'emplacement cliqué
            chart.zoomToGeoPoint(geoPoint, 5); // Ajuste le niveau de zoom
        } else {
            console.warn("Impossible de déterminer les coordonnées géographiques du clic.");
        }

        // Met à jour les informations sur la région
        showRegionInfo(regionName);
    });

    // Afficher les informations de la région
    function showRegionInfo(regionName) {
        const infoPanel = document.getElementById("info-panel");
        infoPanel.innerHTML = `
                    <div class="info-card">
                        <h2>${regionName}</h2>
                        <p>Cette région est connue pour ses vins uniques.</p>
                    </div>
                `;
    }
});