<?php

use App\Blog;
use App\Template;

require dirname(__DIR__) . '/vendor/autoload.php';

\Dotenv\Dotenv::createImmutable(dirname(__DIR__))->safeLoad();

header('Content-Type: application/json; charset=utf-8');

$isAjax = !empty($_REQUEST['is_ajax']);

if ($isAjax) {
    $params = $_REQUEST;
    $perPage = filter_var(getenv('CATEGORIES_PER_PAGE'), FILTER_VALIDATE_INT);
    $parentId = isset($_REQUEST['category_id']) ? max(0, (int) $_REQUEST['category_id']) : 0;

    try {
        $paginator = (new Blog())->getPaginatedCategories($perPage, $params);
    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Unable to load']);
        exit;
    }

    Template::init();

    $html = '';

    foreach ($paginator->getItems() as $category) {
        Template::getSmarty()->assign('category', $category);
        Template::getSmarty()->assign('selected_category_id', $parentId);
        $html .= Template::getSmarty()->fetch('components/blog/category.tpl');
    }

    echo json_encode([
        'html' => $html,
        'categories' => array_map(
            fn ($category) => ['id' => (int) $category['id'], 'title' => $category['title']],
            $paginator->getItems()
        ),
        'hasNextPage' => $paginator->hasNextPage()
    ]);
    exit;
}
