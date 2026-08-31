<?php

use App\Blog;
use App\Style;
use App\Template;

require dirname(__DIR__) . '/vendor/autoload.php';

\Dotenv\Dotenv::createImmutable(dirname(__DIR__))->safeLoad();

header('Content-Type: text/html; charset=utf-8');

$posts = $postSortings = [];
$paginator = null;

$params = $_REQUEST;
$page = isset($params['page']) ? max(1, (int) $params['page']) : 1;
$categoryId = isset($_REQUEST['category_id']) ? max(1, (int) $params['category_id']) : 1;
$sort = isset($params['sort']) ? (string) $params['sort'] : '';

try {
    $blog = new Blog();

    $paginator = $blog->getPaginatedPosts(2, $params);
    $posts = $paginator->getItems();

    $postSortings = $blog->getPostsSortingsVariants();

    $categoriesPaginator = $blog->getPaginatedCategories(2, $params);
    $categories = $categoriesPaginator->getItems();

} catch (Throwable $e) {
    die('error: ' . $e->getMessage());
}

Style::init();

Template::init();
Template::getSmarty()->assign([
    'params' => $params,
    'posts' => $posts,
    'paginator' => $paginator,
    'categories' => $categories,
    'postSortings' => $postSortings
]);
Template::getSmarty()->display('blog.tpl');
