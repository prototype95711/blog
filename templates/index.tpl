{$title = "Home page | Blog"}
{$has_pages = $categoriesPaginator->hasNextPage()}

{capture name="content"}
    <h1>Home Page</h1>

    <div class="blog-layout">
        <div class="blog-categories-list">
            {if $categoriesWithRecentPosts}
                <div
                    class="category"
                    id="categories_view"
                    data-page="1"
                    data-has-next="{if $has_pages}true{else}false{/if}"
                >
                    {foreach from=$categoriesWithRecentPosts item=category}
                        {include file="components/home/category_item.tpl"}
                    {/foreach}
                </div>
            {else}
                <p>Empty blog yet.</p>
            {/if}
        </div>
    </div>
{/capture}

{include file="common/contained_page.tpl"}

<script>
(function () {
    var container = document.getElementById('categories_view');

    if (!container) {
        return;
    }

    var page = parseInt(container.dataset.page || '1'),
        hasNext = container.dataset.hasNext === 'true',
        loading = false;

    window.addEventListener('scroll', function () {
        var threshold = 200;

        if (window.scrollY + window.innerHeight >= document.body.scrollHeight - threshold) {
            loadMore();
        }
    });

    function loadMore() {
        if (loading || !hasNext) {
            return;
        }

        loading = true;

        var nextPage = page + 1;
        var url = '/?is_ajax=1&c_page=' + nextPage;

        fetch(url)
            .then(function (response) { return response.json(); })
            .then(function (data) {
                container.insertAdjacentHTML('beforeend', data.html || '');
                page = nextPage;
                hasNext = data.hasNextPage;
                container.dataset.hasNext = hasNext ? 'true' : 'false';
            })
            .catch(function () {
                hasNext = false;
            })
            .finally(function () {
                loading = false;
            });
    }
})();
</script>
