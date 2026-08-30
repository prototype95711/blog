<?php

use App\Style;
use App\Template;

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Database;

\Dotenv\Dotenv::createImmutable(dirname(__DIR__))->safeLoad();

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

Style::init();

Template::init();
Template::getSmarty()->assign('posts', $posts);
Template::getSmarty()->display('index.tpl');
