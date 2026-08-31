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
$postId = isset($params['id']) ? max(0, (int) $params['id']) : 0;

$blog = new Blog();

try {
    $post = $postId > 0 ? $blog->getPost($postId) : null;
} catch (Throwable $e) {
    $post = null;
}

$relatedPosts = [];

if ($post === null) {
    http_response_code(404);
} else {

    try {
        $added = $blog->addPostView($postId);

        if ($added) {
            $post['views']++;
        }

        $relatedPosts = $blog->getRelatedPosts($postId, 3);

    } catch (Throwable $e) {
        die($e->getMessage());
    }
}

Style::init();

Template::init();
Template::getSmarty()->assign([
    'params' => $params,
    'post' => $post,
    'relatedPosts' => $relatedPosts
]);

Template::getSmarty()->display('post.tpl');
