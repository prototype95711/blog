<?php

namespace App\Interface;

interface IPagination
{
    public function getItems(): array;

    public function currentPage(): int;

    public function perPage(): int;

    public function total(): int;

    public function lastPage(): int;

    public function hasNextPage(): bool;

    public function hasPreviousPage(): bool;
}
