<?php

namespace App\Abstraction;

use App\Database;
use App\Interface\IPagination;
use App\Interface\IRepository;
use App\Pagination\Paginator;
use App\Sorting\Sorting;
use PDO;

abstract class ASortedRepository implements IRepository
{
    const SORT_ORDER = ['DESC' => '', 'ASC' => ''];

    /** @var array<Sorting> */
    protected array $sortings = [];

    protected abstract function initSortings() : void;

    public function __construct()
    {
        $this->initSortings();

        $this->sortings = array_filter($this->sortings, function ($sorting) {
            return $sorting instanceof Sorting;
        });
    }

    /**
     * @return array<Sorting>
     */
    public function getSortings() : array
    {
        return $this->sortings;
    }

    public function getSortingOrders() : array
    {
        return array_keys(self::SORT_ORDER);
    }

    protected function hasSorting(string $alias) : bool
    {
        return $this->getSortingField($alias) !== null;
    }

    protected function getFirstSorting() : string|null
    {
        return empty($this->sortings) ? null : reset($this->sortings)->getAlias();
    }

    protected function getSortingField(string $alias) : string|null
    {
        foreach ($this->sortings as $sorting) {

            if ($sorting->getAlias() === $alias) {
                return $sorting->field;
            }
        }

        return null;
    }

    protected function getDefaultOrder() : string|null
    {
        return array_key_first(self::SORT_ORDER);
    }
}
