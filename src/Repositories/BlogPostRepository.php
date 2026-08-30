<?php

namespace App\Repositories;

use App\Abstraction\ASortedRepository;
use App\Database;
use App\Interface\IPagination;
use App\Interface\IRepository;
use App\Pagination\Paginator;
use PDO;

class BlogPostRepository extends ASortedRepository implements IRepository
{
    protected function initSortings() : void
    {
        $this->sortings = ['views' => 'posts.views', 'created_at' => 'posts.created_at'];
    }

    public function getList(array $params = [], int $perPage = 0): IPagination
    {
        $page = $params['page'] ?? 1;
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $sortBy = $params['sort_by'] ?? '';
        $sortBy = array_key_exists($sortBy, $this->sortings)
            ? $sortBy
            : $this->getFirstSorting();

        $sortOrder = $params['sort_order'] ?? '';
        $sortOrder = array_key_exists($sortOrder, self::SORT_ORDER)
            ? $sortOrder
            : $this->getDefaultOrder();

        $sortField = $this->getSortingField($sortBy);

        $pdo = Database::connection();
        $request = ' FROM posts as posts'
            . ' INNER JOIN categories_links as categories_links'
                . ' ON categories_links.post_id = posts.id'
            . ' INNER JOIN categories as category'
                . ' ON category.id = categories_links.category_id'
            . ' LEFT JOIN images as image'
                . ' ON image.id = posts.image_id';

        $condition = [];

        if (!empty($condition)) {
            $request .= ' WHERE ' . implode(' AND ', $condition);
        }

        $total = (int) $pdo->query('SELECT COUNT(*) ' . $request)->fetchColumn();
        
        $fullRequest =
            'SELECT posts.id, posts.title, posts.content, posts.created_at'
                . ', category.title as category_title'
                . ', image.filepath as image'
            . $request;


        if ($sortField !== null) {
            $fullRequest .= ' ORDER BY ' . $sortField . ' ' . $sortOrder;
        }

        $isNeedPagination = $perPage > 0;

        if ($isNeedPagination) {
            $fullRequest .= ' LIMIT :limit OFFSET :offset';
        }

        $stmt = $pdo->prepare($fullRequest);

        if ($isNeedPagination) {
            $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }

        $stmt->execute();

        return new Paginator($stmt->fetchAll(), $total, $perPage, $page);
    }
}
