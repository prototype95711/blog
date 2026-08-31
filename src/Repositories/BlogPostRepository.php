<?php

namespace App\Repositories;

use App\Abstraction\ASortedRepository;
use App\Database;
use App\Interface\IPagination;
use App\Interface\IRepository;
use App\Pagination\Paginator;
use App\Sorting\Sorting;
use PDO;

class BlogPostRepository extends ASortedRepository
{
    protected function initSortings() : void
    {
        $this->sortings = [
            new Sorting('posts.views', 'Views', ['DESC' => 'Most viewed', 'ASC' => 'Least viewed']),
            new Sorting('posts.created_at', 'Created At', ['DESC' => 'Newest first', 'ASC' => 'Oldest first'])
        ];
    }

    public function getList(array $params = [], int $perPage = 0): IPagination
    {
        $page = $params['page'] ?? 1;
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $sort = $params['sort'] ?? '';

        if (!empty($sort)) {
            list($params['sort_by'], $params['sort_order']) = Sorting::parseDispatch(
                $sort
            );
        }

        $sortBy = $params['sort_by'] ?? '';

        $sortBy = $this->hasSorting($sortBy)
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

        $condition = $vars = [];
        $categoryId = isset($params['category_id']) ? max(0, $params['category_id']) : 0;

        if ($categoryId > 0) {
            $condition['category_id'] = 'categories_links.category_id = :category_id';
            $vars['category_id'] = $categoryId;
        }

        if (!empty($condition)) {
            $request .= ' WHERE ' . implode(' AND ', $condition);
        }

        $total = $pdo->prepare('SELECT COUNT(*) ' . $request);

        foreach ($vars as $name => $value) {
            $total->bindValue(':' . $name, $value, PDO::PARAM_STR);
        }

        $total->execute();
        $total = (int) $total->fetchColumn();
        
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

        foreach ($vars as $name => $value) {
            $stmt->bindValue(':' . $name, $value, PDO::PARAM_STR);
        }

        if ($isNeedPagination) {
            $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }

        $stmt->execute();

        return new Paginator($stmt->fetchAll(), $total, $perPage, $page);
    }
}
