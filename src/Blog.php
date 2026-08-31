<?php

namespace App;

use App\Abstraction\ASortedRepository;
use App\Interface\IPagination;
use App\Interface\IRepository;
use App\Repositories\BlogPostRepository;
use App\Repositories\CategoryRepository;

class Blog
{
    private ASortedRepository $posts;

    private ASortedRepository $categories;

    public function __construct(?IRepository $posts = null, ?IRepository $categories = null)
    {
        $this->posts = $posts ?? new BlogPostRepository();
        $this->categories = $categories ?? new CategoryRepository();
    }

    public function getPaginatedPosts(int $perPage = 10, array $params = []): IPagination
    {
        return $this->posts->getList($params, $perPage);
    }

    public function getPaginatedCategories(int $perPage = 10, array $params = []): IPagination
    {
        return $this->categories->getList($params, $perPage);
    }

    public function getCategory(int $id): ?array
    {
        return $this->categories->get($id);
    }

    public function getPostsSortingsVariants(): array
    {
        $sotringsToDisplay = [];
        $orders = $this->posts->getSortingOrders();

        foreach ($this->posts->getSortings() as $sorting) {
            $sotringsToDisplay = array_merge($sotringsToDisplay, $sorting->getVariants($orders));      
        }
        
        return $sotringsToDisplay;
    }
}
