<?php

use App\Blog;
use App\Template;

require_once 'init.php';

$params = $_REQUEST;
$blog = new Blog();
$isAjax = !empty($params['is_ajax']);

function prepareCategoriesWithRecentPosts(Blog $blog, array $categories): array
{
    array_walk($categories, function (&$cat) use ($blog) {
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

    return $categories;
}

$perpage = 8;

if ($isAjax) {
    header('Content-Type: application/json; charset=utf-8');

    try {
        $categoriesPaginator = $blog->getCategoriesWithPosts($params, $perpage);
        $categoriesWithRecentPosts = prepareCategoriesWithRecentPosts($blog, $categoriesPaginator->getItems());
    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Unable to load']);
        exit;
    }

    $html = '';

    foreach ($categoriesWithRecentPosts as $category) {
        Template::getSmarty()->assign('category', $category);
        $html .= Template::getSmarty()->fetch('components/home/category_item.tpl');
    }

    echo json_encode([
        'html' => $html,
        'hasNextPage' => $categoriesPaginator->hasNextPage()
    ]);
    exit;
}

$categoriesPaginator = null;
$categoriesWithRecentPosts = [];

try {
    $categoriesPaginator = $blog->getCategoriesWithPosts($params, $perpage);
    $categoriesWithRecentPosts = prepareCategoriesWithRecentPosts($blog, $categoriesPaginator->getItems());
} catch (Throwable $e) {
    die($e->getMessage());
}

Template::getSmarty()->assign([
    'params' => $params,
    'categoriesWithRecentPosts' => $categoriesWithRecentPosts,
    'categoriesPaginator' => $categoriesPaginator
]);
Template::getSmarty()->display('index.tpl');
