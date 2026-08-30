<?php

namespace App\Pagination;

use App\Interface\IPagination;

class Paginator implements IPagination
{
    public function __construct(
        private readonly array $items,
        private readonly int $total,
        private readonly int $perPage,
        private readonly int $currentPage
    ) {
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function currentPage(): int
    {
        return $this->currentPage;
    }

    public function perPage(): int
    {
        return $this->perPage;
    }

    public function total(): int
    {
        return $this->total;
    }

    public function lastPage(): int
    {
        if ($this->perPage <= 0) {
            return 1;
        }

        return (int) max(1, (int) ceil($this->total / $this->perPage));
    }

    public function pages(): array
    {
        return range(1, $this->lastPage());
    }

    public function hasNextPage(): bool
    {
        return $this->currentPage < $this->lastPage();
    }

    public function hasPreviousPage(): bool
    {
        return $this->currentPage > 1;
    }
}
