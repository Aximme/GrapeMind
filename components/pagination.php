<?php
if (!isset($total_results, $results_per_page, $page)) {
    die("Pagination: variables manquantes.");
}

$total_pages = ceil($total_results / $results_per_page);

if ($total_pages > 1): ?>
    <div class="pagination">
        <?php
        if ($page > 1): ?>
            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" class="pagination-link">Précédent</a>
        <?php endif; ?>

        <?php
        $pages_to_show = 3;
        $start_page = max(1, $page - $pages_to_show);
        $end_page = min($total_pages, $page + $pages_to_show);

        if ($start_page > 1): ?>
            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => 1])); ?>" class="pagination-link">1</a>
            <?php if ($start_page > 2): ?>
                <span class="pagination-ellipsis">...</span>
            <?php endif;
        endif;

        for ($i = $start_page; $i <= $end_page; $i++): ?>
            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" class="pagination-link <?php echo $i == $page ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor;

        if ($end_page < $total_pages):
            if ($end_page < $total_pages - 1): ?>
                <span class="pagination-ellipsis">...</span>
            <?php endif; ?>
            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $total_pages])); ?>" class="pagination-link">
                <?php echo $total_pages; ?>
            </a>
        <?php endif; ?>

        <?php
        if ($page < $total_pages): ?>
            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" class="pagination-link">Suivant</a>
        <?php endif; ?>
    </div>
<?php endif; ?>
