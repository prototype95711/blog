<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Blog</title>
</head>
<body>
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
</body>
</html>