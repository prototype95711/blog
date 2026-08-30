{$title = "Blog"}

{capture name="sidebar"}
    {if $categories}
        <div class="sidebar-block">
            <h3 class="sidebar-block-title">Categories</h3>
            {foreach from=$categories item=category name=blog_categories}
                <div class="sidebar-block-item">
                    <a href="#">{$category.title}</a>
                </div>
            {/foreach}
        </div>
    {/if}
{/capture}

{capture name="content"}
    <h1>Blog</h1>

    <div class="blog-layout">
        <div class="blog-posts-list">
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
    <div>

    {if $smarty.capture.sidebar}
        <div class="sidebar">
            {$smarty.capture.sidebar nofilter}
        <div>
    {/if}
{/capture}

{include file="common/contained_page.tpl"}
