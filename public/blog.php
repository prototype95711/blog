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

try {
    $blog = new Blog();
    $paginator = $blog->getPaginatedPosts(10, $_REQUEST);
    $posts = $paginator->getItems();
} catch (Throwable $e) {
    print_r('error: ' . $e->getMessage());
}

Style::init();

Template::init();
Template::getSmarty()->assign('posts', $posts);
Template::getSmarty()->display('blog.tpl');
