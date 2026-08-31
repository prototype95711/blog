{$link = "/blog.php?category_id={$category.id}&sort=CreatedAt:DESC"}
<div class="category-item">
    <div class="category-item-image">
        <a href="{$link}">
            <img src="{$category.image}" />
        </a>
    </div>
    <div class="category-item-data">
        <h3 class="category-item-title">
            <a href="{$link}">{$category.title nofilter}</a>
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
        <a class="primary-button" href="{$link}">All posts</a>
    </div>
</div>
