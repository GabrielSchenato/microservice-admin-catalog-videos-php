<?php

namespace Core\Domain\Repository;

use Core\Domain\Entity\CastMemberEntity;

interface CastMemberRepositoryInterface
{
    public function insert(CastMemberEntity $genre): CastMemberEntity;

    public function findById(string $id): CastMemberEntity;

    public function update(CastMemberEntity $genre): CastMemberEntity;

    public function delete(string $id): bool;

    public function findAll(string $filter = '', $order = 'DESC'): array;

    public function paginate(string $filter = '', $order = 'DESC', int $page = 1, int $totalPage = 15): PaginationInterface;
}
