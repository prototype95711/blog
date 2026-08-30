<?php

use App\Blog;
use App\Style;
use App\Template;

require dirname(__DIR__) . '/vendor/autoload.php';

\Dotenv\Dotenv::createImmutable(dirname(__DIR__))->safeLoad();

header('Content-Type: text/html; charset=utf-8');

$posts = [];
$paginator = null;

$page = isset($_REQUEST['page']) ? max(1, (int) $_REQUEST['page']) : 1;
$categoryId = isset($_REQUEST['category_id']) ? max(1, (int) $_REQUEST['category_id']) : 1;

try {
    $blog = new Blog();

    $paginator = $blog->getPaginatedPosts(2, $_REQUEST);
    $posts = $paginator->getItems();

    $categoriesPaginator = $blog->getPaginatedCategories(2, $_REQUEST);
    $categories = $categoriesPaginator->getItems();

} catch (Throwable $e) {
    die('error: ' . $e->getMessage());
}

Style::init();

Template::init();
Template::getSmarty()->assign([
    'posts' => $posts,
    'paginator' => $paginator,
    'categories' => $categories
]);
Template::getSmarty()->display('blog.tpl');
