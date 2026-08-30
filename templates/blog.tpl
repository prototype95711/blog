{$title = "Blog"}
{capture name="content"}
    <h1>Blog</h1>

    {foreach from=$posts item=post name=posts}
        {include file="components/blog/post.tpl"}

        {if !$smarty.foreach.posts.last}
            <hr />
        {/if}
    {foreachelse}
        <p>No posts</p>
    {/foreach}
{/capture}

{include file="common/contained_page.tpl"}
