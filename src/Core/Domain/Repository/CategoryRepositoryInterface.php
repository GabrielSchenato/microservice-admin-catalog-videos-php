<?php

namespace Core\Domain\Repository;

use Core\Domain\Entity\CategoryEntity;

interface CategoryRepositoryInterface
{
    public function insert(CategoryEntity $category): CategoryEntity;

    public function findById(string $id): CategoryEntity;

    public function update(CategoryEntity $category): CategoryEntity;

    public function delete(string $id): bool;

    public function findAll(string $filter = '', $order = 'DESC'): array;

    public function paginate(string $filter = '', $order = 'DESC', int $page = 1, int $totalPage = 15): PaginationInterface;
}
