<?php

use App\Blog;
use App\Pagination\Paginator;
use App\Style;
use App\Template;
use Detection\MobileDetect;

require dirname(__DIR__) . '/vendor/autoload.php';

\Dotenv\Dotenv::createImmutable(dirname(__DIR__))->safeLoad();

header('Content-Type: text/html; charset=utf-8');

$params = $_REQUEST;
$blog = new Blog();

$categoriesPaginator = null;
$categoriesPerPage = 10;
$categoriesWithRecentPosts = [];

try {
    $categoriesPaginator = $blog->getCategoriesWithPosts($params, 10);
    $categoriesWithRecentPosts = $categoriesPaginator->getItems();

    array_walk($categoriesWithRecentPosts, function (&$cat) use ($blog) {
        $recentPosts = $blog->getPaginatedPosts(3, [
            'category_id' => $cat['id'],
            'sort_by' => 'CreatedAt',
            'sort_order' => 'DESC'
        ])->getItems();

        $cat['recent_posts'] = $recentPosts;

        $firstPost = empty($recentPosts)
            ? null
            : reset($recentPosts);

        $cat['image'] = is_null($firstPost)
            ? ''
            : $firstPost['image'];
    });
} catch (Throwable $e) {
    die($e->getMessage());
}

Style::init();

Template::init();
Template::getSmarty()->assign([
    'params' => $params,
    'categoriesWithRecentPosts' => $categoriesWithRecentPosts,
    'categoriesPaginator' => $categoriesPaginator
]);
Template::getSmarty()->display('index.tpl');
