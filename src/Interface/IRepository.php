<?php

namespace App\Interface;

interface IRepository
{
    public function getList(array $searchParams = [], int $perPage = 0): IPagination;

    public function get(int $id): ?array;
}
