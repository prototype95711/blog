<?php

namespace App\Repositories;

use App\Abstraction\ASortedRepository;
use App\Database;
use App\Interface\IPagination;
use App\Interface\IRepository;
use App\Pagination\Paginator;
use App\Sorting\Sorting;
use PDO;

class CategoryRepository extends ASortedRepository
{
    protected function initSortings() : void
    {
        $this->sortings = [
            new Sorting('categories.id', 'id'),
            new Sorting('categories.title', 'title'),
            new Sorting('MAX(posts.created_at)', 'postCreatedAt')
        ];
    }

    public function getList(array $params = [], int $perPage = 0): IPagination
    {
        $page = $params['c_page'] ?? 1;
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $sortBy = $this->getFirstSorting();
        $sortOrder = $this->getDefaultOrder();
        $sort = $params['sort'] ?? '';

        if (!empty($sort)) {
            list($params['sort_by'], $params['sort_order']) = Sorting::parseDispatch(
                $sort
            );
            $sortBy = $params['sort_by'] ?: $sortBy;
            $sortOrder = $params['sort_order'] ?: $sortOrder;
        }

        $sortField = $this->getSortingField($sortBy);

        $pdo = Database::connection();
        $request = ' FROM categories as categories';

        $condition = $vars = [];
        $parentId = isset($params['category_id']) ? max(0, $params['category_id']) : 0;
        
        $condition['parent_id'] = 'categories.parent_id = :parent_id';
        $vars['parent_id'] = $parentId;

        if (!empty($params['with_posts'])) {
            $request .= ' INNER JOIN categories_links AS categories_links'
                . ' ON categories_links.category_id = categories.id';

            $request .= ' INNER JOIN posts AS posts'
                . ' ON posts.id = categories_links.post_id';
        }

        if (!empty($condition)) {
            $request .= ' WHERE ' . implode(' AND ', $condition);
        }

        $countStmt = $pdo->prepare('SELECT COUNT(DISTINCT categories.id) ' . $request);

        foreach ($vars as $name => $value) {
            $countStmt->bindValue(':' . $name, $value, PDO::PARAM_STR);
        }

        $countStmt->execute();
        $total = (int) $countStmt->fetchColumn();

        $request .= ' GROUP BY categories.id';

        $fullRequest =
            'SELECT categories.id, categories.title, categories.descr, categories.parent_id'
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
        $stmt = $pdo->prepare('SELECT id, title, descr, parent_id FROM categories WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $category = $stmt->fetch();

        return $category !== false ? $category : null;
    }
}
