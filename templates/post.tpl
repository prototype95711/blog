{$title = $post ? "`$post.title` | Blog" : "Post not found | Blog"}

{if $post}
{capture name="breadcrumbs"}
    <ol class="breadcrumbs-list">
        <li class="breadcrumbs-item"><a href="/">Home</a></li>
        <li class="breadcrumbs-item"><a href="/blog.php">Blog</a></li>
        {foreach from=$categoryPath item=crumb}
            <li class="breadcrumbs-item"><a href="/blog.php?category_id={$crumb.id}">{$crumb.title nofilter}</a></li>
        {/foreach}
        <li class="breadcrumbs-item"><span aria-current="page">{$post.title nofilter}</span></li>
    </ol>
{/capture}
{/if}

{capture name="sidebar"}
    {if $relatedPosts}
    <div class="sidebar-block">
        <h3 class="sidebar-block-title">Related posts</h3>
        {foreach from=$relatedPosts item=relatedPost}
            <div class="sidebar-block-item">
                <a class="sidebar-block-item-button" href="/post.php?id={$relatedPost.id}">{$relatedPost.title nofilter}</a>
            </div>
        {/foreach}
    </div>
    {/if}
{/capture}

{capture name="content"}
    <p><a href="/blog.php">&laquo; Back to blog</a></p>
    <div class="blog-layout">
        <div class="blog-posts-list">
            {if $post}
                <article class="blog-post-details">
                    {if $post.image}
                        <div class="blog-post-details-image">
                            <img src="{$post.image}" alt="{$post.title|escape}" />
                        </div>
                    {/if}

                    <h1>{$post.title nofilter}</h1>

                    <div class="blog-post-details-meta">
                        <time>{$post.created_at}</time>
                        {if $post.category_title}
                            &middot; <a href="/blog.php?category_id={$post.category_id}">{$post.category_title}</a>
                        {/if}
                        &middot; {$post.views} views
                    </div>

                    <div class="blog-post-details-content">
                        {$post.content nofilter}
                    </div>
                </article>
            {else}
                <h1>Post not found</h1>
                <p>The post not exist</p>
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
