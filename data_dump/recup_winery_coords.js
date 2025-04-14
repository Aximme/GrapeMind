/**
 * Script qui met a jour les coordonnées GPS des domaines viticoles des vins de notre bdd.

 * Fonction :
 * - Récupère les domaines depuis la db dont les coordonnées sont nulles.
 * - Utilise l’API Nominatim (OpenStreetMap) pour obtenir latitude/longitude à partir du nom du domaine.
 * - Met à jour les champs `winery_lat` et `winery_lon` dans la table `descriptifs`.
 * 
 * Dépendances :
 * - axios (requêtes HTTP)
 * - mysql2/promise (connexion bdd)
 */

const axios = require('axios');
const mysql = require('mysql2/promise');

async function getCoordinates(wineryName) {
    const geoApiUrl = `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(wineryName)}, France&format=json&addressdetails=1&limit=1`;
    const headers = {
        "User-Agent": "GrapeMindApp/1.0"
    };

    try {
        const response = await axios.get(geoApiUrl, { headers });
        if (response.status === 200 && response.data.length > 0) {
            const geoData = response.data[0];
            return { lat: geoData.lat, lon: geoData.lon };
        }
    } catch (error) {
        console.error(`Error fetching coordinates for ${wineryName}:`, error.message);
    }
    return { lat: null, lon: null };
}

async function updateWineryCoordinates() {
    const dbConfig = {
        host: 'localhost',
        user: 'root',
        password: 'root',
        database: 'grape-mind'
    };

    let connection;

    try {
        connection = await mysql.createConnection(dbConfig);

        const [wineries] = await connection.execute(
            "SELECT WineryID, WineryName FROM descriptifs WHERE winery_lat IS NULL OR winery_lon IS NULL"
        );

        for (const winery of wineries) {
            const { WineryID, WineryName } = winery;
            const { lat, lon } = await getCoordinates(WineryName);

            if (lat && lon) {
                await connection.execute(
                    "UPDATE descriptifs SET winery_lat = ?, winery_lon = ? WHERE WineryID = ?",
                    [lat, lon, WineryID]
                );
                console.log(`Updated ${WineryName} with lat: ${lat}, lon: ${lon}`);
            } else {
                console.log(`Coordinates not found for ${WineryName}`);
            }
        }
    } catch (error) {
        console.error("Database error:", error.message);
    } finally {
        if (connection) {
            await connection.end();
        }
    }
}

updateWineryCoordinates();