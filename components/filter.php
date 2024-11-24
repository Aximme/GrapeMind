<?php
$query = isset($query) ? $query : '';
global $conn;

include __DIR__ . '/../db.php';

$queryRegions = "
    SELECT RegionName, COUNT(*) AS wine_count
    FROM descriptifs
    GROUP BY RegionName
    ORDER BY wine_count DESC
";
$resultRegions = $conn->query($queryRegions);

$regionsList = [];
if ($resultRegions && $resultRegions->num_rows > 0) {
    while ($row = $resultRegions->fetch_assoc()) {
        $regionsList[] = ['name' => $row['RegionName'], 'count' => $row['wine_count']];
    }
}

$queryGrapes = "
    SELECT Grapes, COUNT(*) AS wine_count
    FROM descriptifs
    GROUP BY Grapes
    ORDER BY wine_count DESC
";
$resultGrapes = $conn->query($queryGrapes);

$grapesList = [];
if ($resultGrapes && $resultGrapes->num_rows > 0) {
    while ($row = $resultGrapes->fetch_assoc()) {
        $grapes = preg_split('/[\/,]+/', $row['Grapes']);
        foreach ($grapes as $grape) {
            $grape = trim($grape);
            $grape = str_replace(['[', ']', "'"], '', $grape);

            if (!in_array($grape, $grapesList)) {
                $grapesList[] = ['name' => $grape, 'count' => $row['wine_count']];
            }
        }
    }
}

$conn->close();
?>

<link rel="stylesheet" href="/GrapeMind/css/filter.css">

<aside class="filter-panel">
    <h2>Filtres</h2>
    <form method="get" class="filter-form">
        <input type="hidden" name="query" value="<?= htmlspecialchars($query); ?>">

        <div class="filter-section">
            <h3>Prix</h3>
            <label for="minPrice">Prix minimum :</label>
            <input type="number" name="minPrice" id="minPrice" value="<?= isset($_GET['minPrice']) && $_GET['minPrice'] !== '' ? htmlspecialchars($_GET['minPrice']) : ''; ?>" min="0">
            <label for="maxPrice">Prix maximum :</label>
            <input type="number" name="maxPrice" id="maxPrice" value="<?= isset($_GET['maxPrice']) && $_GET['maxPrice'] !== '' && $_GET['maxPrice'] < PHP_INT_MAX ? htmlspecialchars($_GET['maxPrice']) : ''; ?>" min="0">
        </div>

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

        <div class="advanced-filters">
            <button type="button" class="more-filters-button" onclick="toggleFilters()">+ de filtres</button>
            <div id="advanced-filters" style="display: none;">
                <label for="region">Région</label>
                <input type="text" id="region-input" placeholder="Commencez à taper une région...">
                <div id="region-suggestions" class="suggestions"></div>

                <div id="selected-regions" class="selected-tags"></div>

                <label for="grapes">Cépages</label>
                <input type="text" id="grapes-input" placeholder="Commencez à taper un cépage...">
                <div id="grapes-suggestions" class="suggestions"></div>

                <div id="selected-grapes" class="selected-tags"></div>
            </div>
        </div>

        <button type="submit" class="filter-submit">Appliquer les filtres</button>
    </form>
</aside>

<script>
    const regionInput = document.getElementById('region-input');
    const regionSuggestions = document.getElementById('region-suggestions');
    const selectedRegionsContainer = document.getElementById('selected-regions');
    const selectedRegions = [];

    const grapesInput = document.getElementById('grapes-input');
    const grapesSuggestions = document.getElementById('grapes-suggestions');
    const selectedGrapesContainer = document.getElementById('selected-grapes');
    const selectedGrapes = [];

    const regionsList = <?php echo json_encode($regionsList); ?>;
    const grapesList = <?php echo json_encode($grapesList); ?>;

    function displaySuggestions(input, suggestionsContainer, itemsList, selectedItems, addItemFunction) {
        input.addEventListener('input', function () {
            const query = input.value.toLowerCase();
            suggestionsContainer.innerHTML = '';

            if (query.length > 0) {
                const filteredItems = itemsList.filter(item =>
                    item.name.toLowerCase().includes(query)
                );

                const limitedItems = filteredItems.slice(0, 5);

                if (limitedItems.length > 0) {
                    limitedItems.forEach(item => {
                        const suggestionItem = document.createElement('div');
                        suggestionItem.textContent = `${item.name} (${item.count})`;
                        suggestionItem.addEventListener('click', () => addItemFunction(item.name));
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

    function addItem(item, selectedItems, selectedContainer, input, suggestionsContainer, updateInputFunction) {
        if (!selectedItems.includes(item)) {
            selectedItems.push(item);
            createTag(item, selectedItems, selectedContainer, updateInputFunction);
            updateInputFunction();
            suggestionsContainer.innerHTML = '';
            input.value = '';
        }
    }

    function createTag(item, selectedItems, selectedContainer, updateInputFunction) {
        const tag = document.createElement('div');
        tag.classList.add('tag');

        const textSpan = document.createElement('span');
        textSpan.textContent = item;

        const removeButton = document.createElement('span');
        removeButton.classList.add('remove-item');
        removeButton.textContent = 'x';
        removeButton.addEventListener('click', () => {
            const index = selectedItems.indexOf(item);
            if (index > -1) {
                selectedItems.splice(index, 1);
                tag.remove();
                updateInputFunction();
            }
        });

        tag.appendChild(textSpan);
        tag.appendChild(removeButton);
        selectedContainer.appendChild(tag);
    }

    function updateRegionInput() {
        document.querySelectorAll('input[name="region[]"]').forEach(el => el.remove());

        if (selectedRegions.length > 0) {
            selectedRegions.forEach(region => {
                const regionInputHidden = document.createElement('input');
                regionInputHidden.type = 'hidden';
                regionInputHidden.name = 'region[]';
                regionInputHidden.value = region;
                document.querySelector('form').appendChild(regionInputHidden);
            });
        }
    }

    function updateGrapeInput() {
        document.querySelectorAll('input[name="grapes[]"]').forEach(el => el.remove());

        if (selectedGrapes.length > 0) {
            selectedGrapes.forEach(grape => {
                const grapeInputHidden = document.createElement('input');
                grapeInputHidden.type = 'hidden';
                grapeInputHidden.name = 'grapes[]';
                grapeInputHidden.value = grape;
                document.querySelector('form').appendChild(grapeInputHidden);
            });
        }
    }

    displaySuggestions(
        regionInput,
        regionSuggestions,
        regionsList,
        selectedRegions,
        (region) => addItem(region, selectedRegions, selectedRegionsContainer, regionInput, regionSuggestions, updateRegionInput)
    );

    displaySuggestions(
        grapesInput,
        grapesSuggestions,
        grapesList,
        selectedGrapes,
        (grape) => addItem(grape, selectedGrapes, selectedGrapesContainer, grapesInput, grapesSuggestions, updateGrapeInput)
    );

    function toggleFilters() {
        const filters = document.getElementById('advanced-filters');
        filters.style.display = filters.style.display === 'none' || filters.style.display === '' ? 'block' : 'none';
    }
</script>
