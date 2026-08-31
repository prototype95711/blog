{$title = "Home page | Blog"}
{$has_pages = $categoriesPaginator->hasNextPage()}

{capture name="content"}
    <h1>Home Page</h1>

    <div class="blog-layout">
        <div class="blog-categories-list">
            {if $categoriesWithRecentPosts}
                <div class="category">
                    {foreach from=$categoriesWithRecentPosts item=category}
                        <div class="category-item">
                            <div class="category-item-image">
                                <a href="/blog.php?category_id={$category.id}">
                                    <img src="{$category.image}" />
                                </a>
                            </div>
                            <div class="category-item-data">
                                <h3 class="category-item-title">
                                    <a href="/blog.php?category_id={$category.id}">{$category.title nofilter}</a>
                                </h3>
                                <ul class="category-item-posts">
                                    {foreach from=$category.recent_posts item=recentPost}
                                        <li>
                                            <a href="/post.php?id={$recentPost.id}">{$recentPost.title nofilter}</a>
                                            <time>{$recentPost.created_at}</time>
                                        </li>
                                    {/foreach}
                                </ul>
                            </div>
                            <div class="category-item-buttons">
                                <a class="primary-button" href="/blog.php?category_id={$category.id}">All posts</a>
                            </div>
                        </div>
                    {/foreach}
                </div>
            {else}
                <p>Empty blog yet.</p>
            {/if}
        </div>
    </div>
{/capture}

{include file="common/contained_page.tpl"}
