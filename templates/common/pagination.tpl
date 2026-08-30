{$categorySuffix = $activeCategoryId ? "&category_id=`$activeCategoryId`" : ""}
<nav class="pagination" aria-label="Pagination">
    {if $paginator->hasPreviousPage()}
        <a class="pagination-item pagination-prev" href="?page={$paginator->currentPage() - 1}{$categorySuffix nofilter}">&laquo; Prev</a>
    {/if}

    {foreach from=$paginator->pages() item=pageNumber}
        {if $pageNumber == $paginator->currentPage()}
            <span class="pagination-item pagination-current">{$pageNumber}</span>
        {else}
            <a class="pagination-item" href="?page={$pageNumber}{$categorySuffix nofilter}">{$pageNumber}</a>
        {/if}
    {/foreach}

    {if $paginator->hasNextPage()}
        <a class="pagination-item pagination-next" href="?page={$paginator->currentPage() + 1}{$categorySuffix nofilter}">Next &raquo;</a>
    {/if}
</nav>
