<?php

namespace Core\Domain\Repository;

use Core\Domain\Entity\GenreEntity;

interface GenreRepositoryInterface
{
    public function insert(GenreEntity $genre): GenreEntity;

    public function findById(string $id): GenreEntity;

    public function getIdsListIds(array $genresId = []): array;

    public function update(GenreEntity $genre): GenreEntity;

    public function delete(string $id): bool;

    public function findAll(string $filter = '', $order = 'DESC'): array;

    public function paginate(string $filter = '', $order = 'DESC', int $page = 1, int $totalPage = 15): PaginationInterface;
}
