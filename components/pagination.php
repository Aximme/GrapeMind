<?php if ($total_pages > 1): ?>
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?query=<?php echo urlencode($query); ?>&page=<?php echo $page - 1; ?>" class="pagination-link">Précédent</a>
        <?php endif; ?>

        <?php
        // Définir le nombre de pages visibles autour de la page actuelle
        $pages_to_show = 3;

        // Définir les limites de l'affichage des pages
        $start_page = max(1, $page - $pages_to_show);
        $end_page = min($total_pages, $page + $pages_to_show);

        // Afficher la première page et "..."
        if ($start_page > 1) {
            echo '<a href="?query=' . urlencode($query) . '&page=1" class="pagination-link">1</a>';
            if ($start_page > 2) {
                echo '<span class="pagination-ellipsis">...</span>';
            }
        }

        // Afficher les pages autour de la page actuelle
        for ($i = $start_page; $i <= $end_page; $i++) {
            echo '<a href="?query=' . urlencode($query) . '&page=' . $i . '" class="pagination-link ' . ($i == $page ? 'active' : '') . '">' . $i . '</a>';
        }

        // Afficher la dernière page et "..."
        if ($end_page < $total_pages) {
            if ($end_page < $total_pages - 1) {
                echo '<span class="pagination-ellipsis">...</span>';
            }
            echo '<a href="?query=' . urlencode($query) . '&page=' . $total_pages . '" class="pagination-link">' . $total_pages . '</a>';
        }
        ?>

        <?php if ($page < $total_pages): ?>
            <a href="?query=<?php echo urlencode($query); ?>&page=<?php echo $page + 1; ?>" class="pagination-link">Suivant</a>
        <?php endif; ?>
    </div>
<?php endif; ?>
