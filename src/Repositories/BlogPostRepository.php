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
            'SELECT posts.id, posts.title, posts.content, posts.created_at, posts.views'
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

    public function get(int $id): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT posts.id, posts.title, posts.content, posts.created_at, posts.views'
                . ', category.id as category_id, category.title as category_title'
                . ', image.filepath as image'
            . ' FROM posts as posts'
            . ' LEFT JOIN categories_links as categories_links'
                . ' ON categories_links.post_id = posts.id'
            . ' LEFT JOIN categories as category'
                . ' ON category.id = categories_links.category_id'
            . ' LEFT JOIN images as image'
                . ' ON image.id = posts.image_id'
            . ' WHERE posts.id = :id'
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $post = $stmt->fetch();

        return $post !== false ? $post : null;
    }

    public function addViews(int $id): bool
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('UPDATE posts SET views = views + 1 WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return true;
    }

    public function getRelated(int $id, int $limit = 5): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT posts.id, posts.title, posts.created_at'
                . ', COUNT(DISTINCT shared.category_id) as shared_categories'
            . ' FROM posts as posts'
            . ' INNER JOIN categories_links as shared'
                . ' ON shared.post_id = posts.id'
            . ' WHERE posts.id != :id'
                . ' AND shared.category_id IN ('
                    . ' SELECT category_id FROM categories_links WHERE post_id = :id'
                . ' )'
            . ' GROUP BY posts.id, posts.title, posts.created_at'
            . ' ORDER BY shared_categories DESC, posts.created_at DESC'
            . ' LIMIT :limit'
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
