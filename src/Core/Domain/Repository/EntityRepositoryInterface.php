<?php

namespace Core\Domain\Repository;

use Core\Domain\Entity\AbstractEntity;

interface EntityRepositoryInterface
{
    public function insert(AbstractEntity $entity): AbstractEntity;

    public function findById(string $id): AbstractEntity;

    public function update(AbstractEntity $entity): AbstractEntity;

    public function delete(string $id): bool;

    public function findAll(string $filter = '', $order = 'DESC'): array;

    public function paginate(string $filter = '', $order = 'DESC', int $page = 1, int $totalPage = 15): PaginationInterface;
}
