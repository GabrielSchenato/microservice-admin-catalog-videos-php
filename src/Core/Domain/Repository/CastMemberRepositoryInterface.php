<?php

namespace Core\Domain\Repository;

use Core\Domain\Entity\CastMemberEntity;

interface CastMemberRepositoryInterface
{
    public function insert(CastMemberEntity $castMember): CastMemberEntity;

    public function findById(string $id): CastMemberEntity;

    public function getIdsListIds(array $castMembersId = []): array;

    public function update(CastMemberEntity $castMember): CastMemberEntity;

    public function delete(string $id): bool;

    public function findAll(string $filter = '', $order = 'DESC'): array;

    public function paginate(string $filter = '', $order = 'DESC', int $page = 1, int $totalPage = 15): PaginationInterface;
}
