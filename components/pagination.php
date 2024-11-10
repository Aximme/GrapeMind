<?php
// Vérifiez que les variables nécessaires sont définies
if (!isset($total_results, $results_per_page, $page)) {
    die("Pagination: variables manquantes.");
}

// Calculer le nombre total de pages
$total_pages = ceil($total_results / $results_per_page);

// Afficher la pagination seulement si on a plus d'une page
if ($total_pages > 1): ?>
    <div class="pagination">
        <?php
        // Lien vers la page précédente
        if ($page > 1): ?>
            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" class="pagination-link">Précédent</a>
        <?php endif; ?>

        <?php
        // Définir la plage de pages à afficher autour de la page actuelle
        $pages_to_show = 3;
        $start_page = max(1, $page - $pages_to_show);
        $end_page = min($total_pages, $page + $pages_to_show);

        // Lien vers la première page et "..."
        if ($start_page > 1): ?>
            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => 1])); ?>" class="pagination-link">1</a>
            <?php if ($start_page > 2): ?>
                <span class="pagination-ellipsis">...</span>
            <?php endif;
        endif;

        // Boucle pour afficher les liens des pages autour de la page actuelle
        for ($i = $start_page; $i <= $end_page; $i++): ?>
            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" class="pagination-link <?php echo $i == $page ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor;

        // Lien vers la dernière page et "..."
        if ($end_page < $total_pages):
            if ($end_page < $total_pages - 1): ?>
                <span class="pagination-ellipsis">...</span>
            <?php endif; ?>
            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $total_pages])); ?>" class="pagination-link">
                <?php echo $total_pages; ?>
            </a>
        <?php endif; ?>

        <?php
        // Lien vers la page suivante
        if ($page < $total_pages): ?>
            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" class="pagination-link">Suivant</a>
        <?php endif; ?>
    </div>
<?php endif; ?>
