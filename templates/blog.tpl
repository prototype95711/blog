{$title = "Blog"}
{$selected_category_id = $params.category_id|default:0}
{$selected_sorting_id = $params.sort|default:0}
{$has_pages = $categoriesPaginator->hasNextPage()}

{if $selectedCategory}
    {$title = "`$selectedCategory.title` | `$title`"}
{else}
    {$selected_category_id = 0}
{/if}

{capture name="sidebar"}
    <div class="sidebar-block">
        <h3 class="sidebar-block-title">Categories</h3>
        <div class="sidebar-block-items sidebar-block-categories {if $has_pages}has-more{/if}"
            data-has-next="{if $has_pages}true{else}false{/if}"
            data-category-id="{$selected_category_id}"
            data-page="1" 
        >
        
        {if $selected_category_id}
            <div class="sidebar-block-item">
                <a href="?category_id={$selectedCategory.parent_id}">..</a> / <span class="active">{$selectedCategory.title}</span>
            </div>
        {/if}
        {foreach from=$categories item=category name=blog_categories}
            {$is_last = $smarty.foreach.blog_categories.last.id == $category.id}
            {include file="components/blog/category.tpl"}
        {foreachelse}
            <small>No categories yet.</small>
        {/foreach}
        </div>
    </div>
{/capture}

{capture name="content"}
    <h1>Blog</h1>

    <div class="blog-layout">
        <div class="blog-posts-list">
            <form class="blog-sort" method="get">
                {if $selected_category_id}<input type="hidden" name="category_id" value="{$selected_category_id}">{/if}
                {if $postSortings}
                <label for="sort">Sort by:</label>
                <select id="sort" name="sort" onchange="this.form.submit()">
                    {foreach from=$postSortings item=sorting}
                        <option value="{$sorting.dispatch}" {if $selected_sorting_id == $sorting.dispatch}selected{/if}>
                            {$sorting.name|capitalize}
                        </option>
                    {/foreach}
                </select>
                <noscript><button type="submit">Sort</button></noscript>
                {/if}
            </form>

            {foreach from=$posts item=post name=blog_items}
                {include file="components/blog/post.tpl"}

                {if !$smarty.foreach.blog_items.last}
                    <hr />
                {/if}
            {foreachelse}
                <p>No posts found</p>
            {/foreach}
            
            {if $paginator->lastPage() > 1}
                {include file="common/pagination.tpl"}
            {/if}
        </div>
        {if $smarty.capture.sidebar}
            <div class="sidebar sticky">
                {$smarty.capture.sidebar nofilter}
            </div>
        {/if}
    </div>
{/capture}

{include file="common/contained_page.tpl"}

<script>
(function () {
    var container = document.querySelector('.sidebar-block-categories');

    if (!container) {
        return;
    }

    var page = parseInt(container.dataset.page || '1'),
        hasNext = container.dataset.hasNext === 'true',
        categoryId = container.dataset.categoryId || '',
        loading = false;

    container.addEventListener('scroll', function () {
        var threshold = 40;

        if (container.scrollTop + container.clientHeight >= container.scrollHeight - threshold) {
            loadMore();
        }
    });

    fillUntilScrollable();

    function loadMore() {

        if (loading || !hasNext) {
            return;
        }

        loading = true;

        var nextPage = page + 1;
        var url = '/categories.php?is_ajax=1&c_page=' + nextPage
            + (categoryId ? '&category_id=' + encodeURIComponent(categoryId) : '');

        fetch(url)
            .then(function (response) { return response.json(); })
            .then(function (data) {
                container.insertAdjacentHTML('beforeend', data.html || '');
                page = nextPage;
                hasNext = !!data.hasNextPage;
                container.classList.toggle('has-more', hasNext);
            })
            .catch(function () {
                hasNext = false;
                container.classList.remove('has-more');
            })
            .finally(function () {
                loading = false;
                fillUntilScrollable();
            });
    }

    function isOverflowing() {
        return container.scrollHeight > container.clientHeight;
    }

    function fillUntilScrollable() {
        if (hasNext && !isOverflowing()) {
            loadMore();
        }
    }
})();
</script>

