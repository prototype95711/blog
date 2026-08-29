<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Database;

header('Content-Type: text/html; charset=utf-8');

$dbStatus = 'unknown';
$posts = [];

try {
    $pdo = Database::connection();
    $dbStatus = 'connected';
    $result = $pdo->query(
        'SELECT posts.id, posts.title, posts.content, posts.created_at'
            . ', category.title as category_title'
            . ', image.filepath as image'
        . ' FROM posts as posts'
        . ' INNER JOIN categories_links as categories_links'
            . ' ON categories_links.post_id = posts.id'
        . ' INNER JOIN categories as category'
            . ' ON category.id = categories_links.category_id'
        . ' LEFT JOIN images as image'
            . ' ON image.id = posts.image_id'
        . ' ORDER BY created_at DESC'
    );
    $posts = $result->fetchAll();
} catch (Throwable $e) {
    $dbStatus = 'error: ' . $e->getMessage();
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Blog</title>
</head>
<body>
    <h1>Blog</h1>

    <?php foreach ($posts as $post): ?>
        <article>
            <h2><?= htmlspecialchars($post['title']) ?></h2>
            <small><?= htmlspecialchars($post['category_title']) ?></small>
            <?php if (!empty($post['image'])) { ?>
                <img src="><?= htmlspecialchars($post['image']) ?>" />
            <?php } ?>
            <p><?= htmlspecialchars($post['content']) ?></p>
            <time><?= htmlspecialchars($post['created_at']) ?></time>
        </article>
    <?php endforeach; ?>

    <?php if (!$posts && $dbStatus === 'connected'): ?>
        <p>No posts</p>
    <?php endif; ?>
</body>
</html>
