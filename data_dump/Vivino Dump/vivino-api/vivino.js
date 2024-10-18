import puppeteer from 'puppeteer';
import minimist from 'minimist';
import fs from 'fs-extra';
import xlsx from 'xlsx';
import pLimit from 'p-limit'; // Importation de p-limit

// Chemin du fichier Excel contenant les noms des vins
const EXCEL_FILE_PATH = '/Users/simon/Desktop/projet-scraping-vin/vins2_2.xlsx';
const OUTPUT_EXCEL_FILE = '/Users/simon/Desktop/projet-scraping-vin/vivino-out.xlsx';

// Fonction pour lire les noms des vins depuis le fichier Excel
const readWineNamesFromExcel = (filePath) => {
	const workbook = xlsx.readFile(filePath);
	const sheetName = workbook.SheetNames[0]; // Lire la première feuille
	const sheet = workbook.Sheets[sheetName];
	const data = xlsx.utils.sheet_to_json(sheet, { header: 1 }); // Récupérer toutes les lignes
	return data.map(row => row[17]).filter(name => name); // Extraire les noms à l'index 17
};

// Fonction pour sauvegarder les résultats dans un fichier Excel
const saveResultsToExcel = (results, filePath) => {
	const ws = xlsx.utils.json_to_sheet(results);
	const wb = xlsx.utils.book_new();
	xlsx.utils.book_append_sheet(wb, ws, 'Results');
	xlsx.writeFile(wb, filePath);
};

// Fonction pour tenter le scraping
const attemptScrape = async (wineName, bestResults) => {
	const BASE_URL = 'https://www.vivino.com';
	const SEARCH_PATH = '/search/wines?q=';

	let browser;
	try {
		browser = await puppeteer.launch({
			headless: true,
			defaultViewport: { width: 1920, height: 1040 },
			devtools: false,
			args: ['--start-maximized'],
		});

		const page = await browser.newPage();

		await page.setUserAgent(
			'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36',
		);

		// Timeout de 45 secondes pour la navigation
		page.setDefaultNavigationTimeout(45000);

		// Rechercher le vin sur Vivino
		const response = await page.goto(`${BASE_URL}${SEARCH_PATH}${wineName}`, { waitUntil: 'networkidle2' });

		if (response.ok()) {
			// Collecter les éléments de la page
			const pageItems = await page.evaluate(() => {
				const numerize = (stringNumber) => {
					const str = stringNumber.replace(/[^0-9,.]+/g, '').replace(',', '.');
					return parseFloat(str);
				};

				const CARDS_SELECTOR = '.card.card-lg';
				const NAME_SELECTOR = '.wine-card__name';
				const AVERAGE_RATING_SELECTOR = '.average__number';
				const RATINGS_SELECTOR = '.average__stars .text-micro';
				const LINK_SELECTOR = 'a';
				const THUMB_SELECTOR = 'figure';
				const THUMB_REGEX = /"(.*)"/;
				const PRICE_SELECTOR = '.wine-price-value';
				const USERS_PRICE_SELECTOR = '.purchaseAvailabilityPPC__betterValueSentence--3OMTX';
				const USERS_PRICE_AMOUNT_SELECTOR = '.purchaseAvailabilityPPC__amount--2_4GT';
				
				// Collecter les informations de la page
				const data = [...document.querySelectorAll(CARDS_SELECTOR)].map((e) => {
					const name = e.querySelector(NAME_SELECTOR).textContent.trim();
					const link = e.querySelector(LINK_SELECTOR).href;
					const thumb = e.querySelector(THUMB_SELECTOR)
						? 'https:' + e.querySelector(THUMB_SELECTOR).style.backgroundImage.match(THUMB_REGEX)[1]
						: undefined;
					const average_rating = e.querySelector(AVERAGE_RATING_SELECTOR)
						? numerize(e.querySelector(AVERAGE_RATING_SELECTOR).textContent.trim())
						: undefined;
					const ratings = e.querySelector(RATINGS_SELECTOR)
						? Number(e.querySelector(RATINGS_SELECTOR).textContent.replace('ratings', '').trim())
						: undefined;
				
					// Combiner tous les prix dans une seule variable
					const price = e.querySelector(PRICE_SELECTOR)
					? numerize(e.querySelector(PRICE_SELECTOR).textContent.trim())
					: e.querySelector(USERS_PRICE_SELECTOR)
					? e.querySelector(USERS_PRICE_SELECTOR).textContent.match(/\$(\d+,\d{2})/)[0]
					: e.querySelector(USERS_PRICE_AMOUNT_SELECTOR)
					? e.querySelector(USERS_PRICE_AMOUNT_SELECTOR).textContent.trim()
					: null;
				
				
					return {
						name: name,
						link: link,
						thumb: thumb,
						average_rating: average_rating,
						ratings: ratings,
						price: price // Tous les prix combinés ici
					};
				});
				return data;				
			});

			if (pageItems.length > 0) {
				console.log(`Result collected for ${wineName}`);
				bestResults.push(pageItems[0]); // Prendre seulement le premier résultat
			} else {
				console.log(`No results found for ${wineName}`);
			}
		} else {
			console.log(`Error fetching data for ${wineName}: ${response.status()}`);
			throw new Error(`Invalid response code: ${response.status()}`);
		}
	} catch (error) {
		console.log(`Error for ${wineName}: ${error.message}`);
		throw error;
	} finally {
		if (browser) {
			try {
				await browser.close();
			} catch (e) {
				console.log('Error closing browser:', e.message);
			}
		}
	}
};

// Fonction principale pour exécuter le scraping
const run = async (wineNames) => {
	const bestResults = []; // Liste pour stocker les meilleurs résultats
	const limit = pLimit(10); // Limite à 10 requêtes simultanées

	// Créer un tableau de promesses pour tous les vins
	const promises = wineNames.map((name, index) => {
		console.log(`Searching for ${name}`);
		return limit(async () => {
			try {
				await attemptScrape(name, bestResults);
			} catch (error) {
				console.log(`Error scraping ${name}: ${error.message}`);
			}

			// Sauvegarder tous les 20 vins
			if ((index + 1) % 20 === 0) {
				saveResultsToExcel(bestResults, OUTPUT_EXCEL_FILE);
				console.log(`Results updated after ${index + 1} wines.`);
			}
		});
	});

	// Attendre que toutes les promesses se résolvent
	await Promise.all(promises);

	// Sauvegarder les résultats finaux
	if (bestResults.length > 0) {
		saveResultsToExcel(bestResults, OUTPUT_EXCEL_FILE);
		console.log('Final results saved.');
	}

	console.log('Scraping finished.');
};

// Lire les arguments
const args = minimist(process.argv.slice(2));
console.log(args);

// Lire les noms des vins depuis le fichier Excel
const wineNames = readWineNamesFromExcel(EXCEL_FILE_PATH);
run(wineNames);






<!-- HTML !-->
<button class="button-30" role="button">Button 30</button>

/* CSS */
.button-30 {
  align-items: center;
  appearance: none;
  background-color: #FCFCFD;
  border-radius: 4px;
  border-width: 0;
  box-shadow: rgba(45, 35, 66, 0.4) 0 2px 4px,rgba(45, 35, 66, 0.3) 0 7px 13px -3px,#D6D6E7 0 -3px 0 inset;
  box-sizing: border-box;
  color: #36395A;
  cursor: pointer;
  display: inline-flex;
  font-family: "JetBrains Mono",monospace;
  height: 48px;
  justify-content: center;
  line-height: 1;
  list-style: none;
  overflow: hidden;
  padding-left: 16px;
  padding-right: 16px;
  position: relative;
  text-align: left;
  text-decoration: none;
  transition: box-shadow .15s,transform .15s;
  user-select: none;
  -webkit-user-select: none;
  touch-action: manipulation;
  white-space: nowrap;
  will-change: box-shadow,transform;
  font-size: 18px;
}

.bn3:focus {
  box-shadow: #D6D6E7 0 0 0 1.5px inset, rgba(45, 35, 66, 0.4) 0 2px 4px, rgba(45, 35, 66, 0.3) 0 7px 13px -3px, #D6D6E7 0 -3px 0 inset;
}

.bn3:hover {
  box-shadow: rgba(45, 35, 66, 0.4) 0 4px 8px, rgba(45, 35, 66, 0.3) 0 7px 13px -3px, #D6D6E7 0 -3px 0 inset;
  transform: translateY(-2px);
}

.bn3:active {
  box-shadow: #D6D6E7 0 3px 7px inset;
  transform: translateY(2px);
}







-- Table Clients
CREATE TABLE Clients (
    clientID INT PRIMARY KEY 
    nom VARCHAR(255) NOT NULL,
    prénom VARCHAR(255) NOT NULL,
    adresse_mail VARCHAR(255) NOT NULL,
    adresse_postale VARCHAR(255) NOT NULL
);

-- Table Wines
CREATE TABLE Wines (
    WineID INT PRIMARY KEY
    Type VARCHAR(100) NOT NULL,
    Elaborate TEXT,
    Grapes TEXT,
    Harmonize TEXT,
    ABV VAARCHAR(100)
    Body VARCHAR(50),
    Acidity VARCHAR(50),
    regionID INT,
    regionName VARCHAR(255),
    wineryID INT,
    wineryName VARCHAR(255),
    wineryWebsite VARCHAR(255),
    Vintages TEXT,
    nameWine_withWinery VARCHAR(255),
    averageRating DECIMAL(3, 2),
    ratings INT,
    price DECIMAL(10, 2),
    flavorGroup VARCHAR(100)
);

-- Table Grenier
CREATE TABLE Grenier (
    WineID INT,
    addedOn DATE,
    wineSheet TEXT,
    FOREIGN KEY (WineID) REFERENCES Wines(WineID)
);

-- Table Cave
CREATE TABLE Cave (
    WineID INT,
    addedOn DATE,
    wineSheet TEXT,
    FOREIGN KEY (WineID) REFERENCES Wines(WineID)
);

-- Table Evénements
CREATE TABLE Evenements (
    evenementID INT PRIMARY KEY AUTO_INCREMENT,
    Date DATE NOT NULL,
    Location VARCHAR(255),
    Time TIME,
    Price DECIMAL(10, 2),
    Contact VARCHAR(255)
);
