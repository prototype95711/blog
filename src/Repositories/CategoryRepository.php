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
            new Sorting('categories.title', 'title')
        ];
    }

    public function getList(array $params = [], int $perPage = 0): IPagination
    {
        $page = $params['c_page'] ?? 1;
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $sortBy = $this->getFirstSorting();
        $sortOrder = $this->getDefaultOrder();

        $sortField = $this->getSortingField($sortBy);

        $pdo = Database::connection();
        $request = ' FROM categories as categories';

        $condition = $vars = [];
        $parentId = isset($params['parent_id']) ? max(0, $params['parent_id']) : 0;

        if ($parentId > 0) {
            $condition['parent_id'] = 'categories.parent_id = :parent_id';
            $vars['parent_id'] = $parentId;
        }

        if (!empty($condition)) {
            $request .= ' WHERE ' . implode(' AND ', $condition);
        }

        $countStmt = $pdo->prepare('SELECT COUNT(*) ' . $request);

        foreach ($vars as $name => $value) {
            $countStmt->bindValue(':' . $name, $value, PDO::PARAM_INT);
        }

        $countStmt->execute();
        $total = (int) $countStmt->fetchColumn();

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
            $stmt->bindValue(':' . $name, $value, PDO::PARAM_INT);
        }

        if ($isNeedPagination) {
            $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }

        $stmt->execute();

        return new Paginator($stmt->fetchAll(), $total, $perPage, $page);
    }
}
