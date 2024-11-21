<?php
$query = isset($query) ? $query : '';
global $conn;

// Inclure la connexion à la base de données
include __DIR__ . '/../db.php';

// Requête pour récupérer toutes les régions distinctes de la base de données
$queryRegions = "SELECT DISTINCT RegionName FROM descriptifs";  // Adapte cette requête selon ta table de vins
$resultRegions = $conn->query($queryRegions);

$regionsList = [];
if ($resultRegions && $resultRegions->num_rows > 0) {
    while ($row = $resultRegions->fetch_assoc()) {
        $regionsList[] = $row['RegionName'];  // Assure-toi que 'RegionName' est correct
    }
}

// Requête pour récupérer tous les cépages distincts de la base de données
$queryGrapes = "SELECT DISTINCT Grapes FROM descriptifs";  // Adapte cette requête selon ta table de vins
$resultGrapes = $conn->query($queryGrapes);

$grapesList = [];
if ($resultGrapes && $resultGrapes->num_rows > 0) {
    while ($row = $resultGrapes->fetch_assoc()) {
        // Si le cépage contient plusieurs cépages séparés par une virgule ou un slash, les séparer
        $grapes = preg_split('/[\/,]+/', $row['Grapes']); // Divise par ',' ou '/'
        foreach ($grapes as $grape) {
            $grape = trim($grape); // Enlever les espaces superflus

            // Supprimer les crochets et les apostrophes
            $grape = str_replace(['[', ']', "'"], '', $grape);

            if (!in_array($grape, $grapesList)) {
                $grapesList[] = $grape;
            }
        }
    }
}

// Fermer la connexion à la base de données
$conn->close();
?>

<link rel="stylesheet" href="/GrapeMind/css/filter.css">

<aside class="filter-panel">
    <h2>Filtres</h2>
    <form method="get" class="filter-form">
        <input type="hidden" name="query" value="<?= htmlspecialchars($query); ?>">

        <!-- Filtre de prix -->
        <div class="filter-section">
            <h3>Prix</h3>
            <label for="minPrice">Prix minimum :</label>
            <input
                    type="number"
                    name="minPrice"
                    id="minPrice"
                    value="<?= isset($_GET['minPrice']) && $_GET['minPrice'] !== '' ? htmlspecialchars($_GET['minPrice']) : ''; ?>"
                    min="0"
            >
            <label for="maxPrice">Prix maximum :</label>
            <input
                    type="number"
                    name="maxPrice"
                    id="maxPrice"
                    value="<?= isset($_GET['maxPrice']) && $_GET['maxPrice'] !== '' && $_GET['maxPrice'] < PHP_INT_MAX ? htmlspecialchars($_GET['maxPrice']) : ''; ?>"
                    min="0"
            >
        </div>

        <!-- Filtre de notation -->
        <div class="filter-section">
            <h3>Notation minimum :</h3>
            <div class="rating-stars">
                <?php $minRating = isset($_GET['minRating']) ? (int)$_GET['minRating'] : 0; ?>
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <input type="radio" id="star-<?= $i ?>" name="minRating" value="<?= $i ?>" <?= $minRating == $i ? 'checked' : ''; ?>>
                    <label for="star-<?= $i ?>" class="star">&#9733;</label>
                <?php endfor; ?>
            </div>
        </div>

        <!-- Filtre de couleur du vin -->
        <div class="filter-section">
            <h3>Couleur du vin :</h3>
            <?php $wineColors = isset($_GET['wineColor']) ? $_GET['wineColor'] : []; ?>
            <div class="wine-options">
                <?php
                $colors = [
                    'Red' => 'Rouge',
                    'Rosé' => 'Rosé',
                    'White' => 'Blanc',
                    'Sparkling' => 'Bulle'
                ];
                foreach ($colors as $value => $label):
                    $isChecked = in_array($value, $wineColors) ? 'checked' : '';
                    ?>
                    <label>
                        <input type="checkbox" name="wineColor[]" value="<?= $value ?>" <?= $isChecked; ?>>
                        <?= $label ?> <span class="<?= strtolower($value) ?>-circle"></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Section des filtres avancés -->
        <div class="advanced-filters">
            <button type="button" class="more-filters-button" onclick="toggleFilters()">+ de filtres</button>
            <div id="advanced-filters" style="display: none;">
                <!-- Recherche de régions avec suggestions -->
                <label for="region">Région</label>
                <input type="text" id="region-input" placeholder="Commencez à taper une région...">
                <div id="region-suggestions" class="suggestions"></div>

                <!-- Conteneur des régions sélectionnées -->
                <div id="selected-regions" class="selected-tags"></div>

                <!-- Recherche de cépages avec suggestions -->
                <label for="grapes">Cépages</label>
                <input type="text" id="grapes-input" placeholder="Commencez à taper un cépage...">
                <div id="grapes-suggestions" class="suggestions"></div>

                <!-- Conteneur des cépages sélectionnés -->
                <div id="selected-grapes" class="selected-tags"></div>
            </div>
        </div>

        <!-- Bouton pour soumettre les filtres -->
        <button type="submit" class="filter-submit">Appliquer les filtres</button>
    </form>
</aside>

<script>
    // Initialisation des variables pour la gestion des régions et cépages
    const regionInput = document.getElementById('region-input');
    const regionSuggestions = document.getElementById('region-suggestions');
    const selectedRegionsContainer = document.getElementById('selected-regions');
    const selectedRegions = [];

    const grapesInput = document.getElementById('grapes-input');
    const grapesSuggestions = document.getElementById('grapes-suggestions');
    const selectedGrapesContainer = document.getElementById('selected-grapes');
    const selectedGrapes = [];

    // Liste des régions et cépages récupérés depuis la base de données en PHP
    const regionsList = <?php echo json_encode($regionsList); ?>;
    const grapesList = <?php echo json_encode($grapesList); ?>;

    // Fonction pour afficher les suggestions pour les régions et cépages
    function displaySuggestions(input, suggestionsContainer, itemsList, selectedItems, addItemFunction) {
        input.addEventListener('input', function () {
            const query = input.value.toLowerCase();
            suggestionsContainer.innerHTML = ''; // Réinitialiser les suggestions

            if (query.length > 0) {
                const filteredItems = itemsList.filter(item =>
                    item.toLowerCase().includes(query)
                );

                // Limiter à 5 suggestions
                const limitedItems = filteredItems.slice(0, 5);

                if (limitedItems.length > 0) {
                    limitedItems.forEach(item => {
                        const suggestionItem = document.createElement('div');
                        suggestionItem.textContent = item; // Affichage sans crochets ni guillemets
                        suggestionItem.addEventListener('click', () => addItemFunction(item));
                        suggestionsContainer.appendChild(suggestionItem);
                    });
                } else {
                    const noResultsItem = document.createElement('div');
                    noResultsItem.textContent = 'Aucun résultat trouvé';
                    suggestionsContainer.appendChild(noResultsItem);
                }
            }
        });
    }

    // Fonction pour ajouter un élément sélectionné (région ou cépage)
    function addItem(item, selectedItems, selectedContainer, input, suggestionsContainer, updateInputFunction) {
        if (!selectedItems.includes(item)) {
            selectedItems.push(item);

            const tag = document.createElement('div');
            tag.classList.add('tag');
            tag.innerHTML = `${item} <span onclick="removeItem('${item}', selectedItems, selectedContainer, updateInputFunction)">x</span>`;
            selectedContainer.appendChild(tag);

            updateInputFunction();
            suggestionsContainer.innerHTML = '';
            input.value = '';
        }
    }

    // Fonction pour supprimer un élément sélectionné (région ou cépage)
    function removeItem(item, selectedItems, selectedContainer, updateInputFunction) {
        const index = selectedItems.indexOf(item);
        if (index > -1) {
            selectedItems.splice(index, 1);
            selectedContainer.removeChild(selectedContainer.childNodes[index]);
            updateInputFunction();
        }
    }

    // Fonction pour mettre à jour les champs cachés pour soumettre les éléments sélectionnés
    function updateRegionInput() {
        const regionInputHidden = document.createElement('input');
        regionInputHidden.type = 'hidden';
        regionInputHidden.name = 'region[]';
        regionInputHidden.value = selectedRegions.join(',');
        document.querySelector('form').appendChild(regionInputHidden);
    }

    function updateGrapeInput() {
        const grapeInputHidden = document.createElement('input');
        grapeInputHidden.type = 'hidden';
        grapeInputHidden.name = 'grapes[]';
        grapeInputHidden.value = selectedGrapes.join(',');
        document.querySelector('form').appendChild(grapeInputHidden);
    }

    // Affichage des suggestions pour les régions et cépages
    displaySuggestions(regionInput, regionSuggestions, regionsList, selectedRegions, (region) => addItem(region, selectedRegions, selectedRegionsContainer, regionInput, regionSuggestions, updateRegionInput));
    displaySuggestions(grapesInput, grapesSuggestions, grapesList, selectedGrapes, (grape) => addItem(grape, selectedGrapes, selectedGrapesContainer, grapesInput, grapesSuggestions, updateGrapeInput));

    // Fonction pour afficher/masquer les filtres avancés
    function toggleFilters() {
        const filters = document.getElementById('advanced-filters');
        if (filters.style.display === 'none' || filters.style.display === '') {
            filters.style.display = 'block';
        } else {
            filters.style.display = 'none';
        }
    }
</script>
