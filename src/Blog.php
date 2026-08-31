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

    public function getCategoriesWithPosts(array $params = [], int $perPage = 10): IPagination
    {
        return $this->categories->getList(
            array_merge($params, ['with_posts' => true, 'category_id' => 0]), 
            $perPage
        );
    }

    public function getPost(int $id): ?array
    {
        return $this->posts->get($id);
    }

    public function addPostView(int $id): bool
    {
        return $this->posts->addViews($id);
    }

    public function getRelatedPosts(int $id, int $limit = 5): array
    {
        return $this->posts->getRelated($id, $limit);
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
