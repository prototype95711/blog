<?php

use App\Blog;
use App\Template;

require_once 'init.php';

$params = $_REQUEST;
$postId = isset($params['id']) ? max(0, (int) $params['id']) : 0;

$blog = new Blog();

try {
    $post = $postId > 0 ? $blog->getPost($postId) : null;
} catch (Throwable $e) {
    $post = null;
}

$relatedPosts = [];
$categoryPath = [];

if ($post === null) {
    http_response_code(404);
} else {

    try {
        $added = $blog->addPostView($postId);

        if ($added) {
            $post['views']++;
        }

        $relatedPosts = $blog->getRelatedPosts($postId, 3);

        if (!empty($post['category_id'])) {
            $categoryPath = $blog->getCategoryPath((int) $post['category_id']);
        }

    } catch (Throwable $e) {
        die($e->getMessage());
    }
}

Template::getSmarty()->assign([
    'params' => $params,
    'post' => $post,
    'relatedPosts' => $relatedPosts,
    'categoryPath' => $categoryPath
]);

Template::getSmarty()->display('post.tpl');
