<?php

namespace App\Abstraction;

use App\Database;
use App\Interface\IPagination;
use App\Interface\IRepository;
use App\Pagination\Paginator;
use PDO;

abstract class ASortedRepository
{
    const SORT_ORDER = ['DESC' => '', 'ASC' => ''];

    protected array $sortings = [];

    protected abstract function initSortings() : void;

    public function __construct()
    {
        $this->initSortings();
    }

    protected function getFirstSorting() : string|null
    {
        return empty($this->sortings) ? null : (string) array_key_first($this->sortings);
    }

    protected function getSortingField(string $sorting) : string|null
    {
        return $this->sortings[$sorting] ?? null;
    }

    protected function getDefaultOrder() : string|null
    {
        return array_key_first(self::SORT_ORDER);
    }
}
