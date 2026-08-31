<article class="blog-post">
    <a href="/post.php?id={$post.id}">
        <div class="blog-post-image">
        {if $post.image}
            <img class="blog-post-image-picture" src="{$post.image}" />
        {else}
            <span class="no-image"></span>
        {/if}
        </div>
    </a>
    <div class="blog-post-short-description">
        <time>{$post.created_at}</time> &middot; {$post.views} views
        <h2 class="blog-post-short-description-title"><a href="/post.php?id={$post.id}">{$post.title nofilter}</a></h2>
        <p class="blog-post-short-description-content">{$post.descr nofilter}</p>
    </div>
</article>
