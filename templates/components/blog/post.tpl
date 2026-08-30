<article class="blog-post">
    <a href="#">
        <div class="blog-post-image">
        {if $post.image}
            <img class="blog-post-image-picture" src="{$post.image}" />
        {else}
            <span class="no-image"></span>
        {/if}
        </div>
    </a>
    <div class="blog-post-short-description">
        <time>{$post.created_at}</time>
        <h2 class="blog-post-short-description-title">{$post.title nofilter}</h2>
        <p class="blog-post-short-description-content">{$post.content nofilter}</p>
    </div>
</article>
