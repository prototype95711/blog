<?php

namespace App;

use App\Interface\IPagination;
use App\Interface\IRepository;
use App\Repositories\BlogPostRepository;

class Blog
{
    private IRepository $posts;

    public function __construct(?IRepository $posts = null)
    {
        $this->posts = $posts ?? new BlogPostRepository();
    }

    public function getPaginatedPosts(int $perPage = 10, array $params = []): IPagination
    {
        return $this->posts->getList($params, $perPage);
    }
}
