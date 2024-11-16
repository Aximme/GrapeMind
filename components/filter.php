<?php
$query = isset($query) ? $query : '';
?>
<link rel="stylesheet" href="/GrapeMind/css/filter.css">
<aside class="filter-panel">
    <h2>Filtres</h2>
    <form method="get" class="filter-form">

        <input type="hidden" name="query" value="<?= htmlspecialchars($query); ?>">

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

        <button type="submit" class="filter-submit">Appliquer les filtres</button>
    </form>
</aside>
