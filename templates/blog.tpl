{$title = "Blog"}
{$selected_category_id = $smarty.request.category_id|default:0}
{$selected_sorting_id = $smarty.request.sort|default:0}

{capture name="sidebar"}
    {if $categories}
        <div class="sidebar-block">
            <h3 class="sidebar-block-title">Categories</h3>
            {foreach from=$categories item=category name=blog_categories}
                <div class="sidebar-block-item">
                    <a href="#" {if $selected_category_id == $category.id}class="active"{/if}>
                        {$category.title}
                    </a>
                </div>
            {/foreach}
        </div>
    {/if}
{/capture}

{capture name="content"}
    <h1>Blog</h1>

    <div class="blog-layout">
        <div class="blog-posts-list">
            <form class="blog-sort" method="get">
                {if $activeCategoryId}<input type="hidden" name="category_id" value="{$activeCategoryId}">{/if}
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
            <div class="sidebar">
                {$smarty.capture.sidebar nofilter}
            </div>
        {/if}
    </div>
{/capture}

{include file="common/contained_page.tpl"}
