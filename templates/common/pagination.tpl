{$category = $params.category_id|default:0 ? "&category_id=`$params.category_id`" : ""}
{$sort = $params.sort|default:'' ? "&sort=`$params.sort`" : ""}
{$extra = "{$category nofilter}{$sort nofilter}"}
<nav class="pagination" aria-label="Pagination">
    {if $paginator->hasPreviousPage()}
        <a class="pagination-item pagination-prev" href="?page={$paginator->currentPage() - 1}{$extra nofilter}">&laquo; Prev</a>
    {/if}

    {foreach from=$paginator->pages() item=pageNumber}
        {if $pageNumber == $paginator->currentPage()}
            <span class="pagination-item pagination-current">{$pageNumber}</span>
        {else}
            <a class="pagination-item" href="?page={$pageNumber}{$extra nofilter}">{$pageNumber}</a>
        {/if}
    {/foreach}

    {if $paginator->hasNextPage()}
        <a class="pagination-item pagination-next" href="?page={$paginator->currentPage() + 1}{$extra nofilter}">Next &raquo;</a>
    {/if}
</nav>
