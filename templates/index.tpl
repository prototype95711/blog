{$title = "Blog"}
{capture name="content"}
    <h1>Blog</h1>

    {foreach from=$posts item=post}
        <article>
            <h2>{$post.title nofilter}</h2>
            <small>{$post.category_title nofilter}</small>
            {if $post.image}
                <img src="{$post.image}" />
            {/if}
            <p>{$post.content nofilter}</p>
            <time>{$post.created_at}</time>
        </article>
    {foreachelse}
        <p>No posts</p>
    {/foreach}
{/capture}

{include file="common/contained_page.tpl"}
