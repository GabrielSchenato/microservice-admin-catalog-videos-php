<?php

namespace App\Repositories\Eloquent;

use App\Models\Genre;
use App\Repositories\Presenters\PaginationPresenter;
use Core\Domain\Entity\GenreEntity;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;
use Core\Domain\ValueObject\Uuid;

class GenreEloquentRepository implements GenreRepositoryInterface
{
    public function __construct(private Genre $model)
    {
    }

    public function insert(GenreEntity $genre): GenreEntity
    {
        $genreCreated = $this->model->create([
            'id' => $genre->id,
            'name' => $genre->name,
            'is_active' => $genre->isActive,
            'created_at' => $genre->createdAt(),
        ]);

        if (count($genre->categoriesId) > 0) {
            $genreCreated->categories()->sync($genre->categoriesId);
        }

        return $this->toGenre($genreCreated);
    }

    public function findById(string $id): GenreEntity
    {
        if (!$genre = $this->model->find($id)) {
            throw new NotFoundException();
        }

        return $this->toGenre($genre);
    }

    public function getIdsListIds(array $categoriesId = []): array
    {
        return $this->model
            ->whereIn('id', $categoriesId)
            ->pluck('id')
            ->toArray();
    }

    public function update(GenreEntity $genre): GenreEntity
    {
        if (!$genreDb = $this->model->find($genre->id())) {
            throw new NotFoundException('Genre Not Found');
        }

        $genreDb->update([
            'name' => $genre->name,
            'is_active' => $genre->isActive
        ]);

        if (count($genre->categoriesId) > 0) {
            $genreDb->categories()->sync($genre->categoriesId);
        }

        $genreDb->refresh();

        return $this->toGenre($genreDb);
    }

    public function delete(string $id): bool
    {
        if (!$genreDb = $this->model->find($id)) {
            throw new NotFoundException('Genre Not Found');
        }
        return $genreDb->delete();
    }

    public function findAll(string $filter = '', $order = 'DESC'): array
    {
        $categories = $this->model
            ->when($filter, fn($query) => $query->where('name', 'LIKE', "%{$filter}%"))
            ->orderBy('id', $order)
            ->get();

        return $categories->toArray();
    }

    public function paginate(string $filter = '', $order = 'DESC', int $page = 1, int $totalPage = 15): PaginationInterface
    {
        $paginator = $this->model
            ->when($filter, fn($query) => $query->where('name', 'LIKE', "%{$filter}%"))
            ->orderBy('id', $order)
            ->paginate();

        return new PaginationPresenter($paginator);
    }

    private function toGenre(object $object): GenreEntity
    {
        $entity = new GenreEntity(
            name: $object->name,
            id: new Uuid($object->id),
            createdAt: $object->created_at
        );
        $object->is_active ? $entity->activate() : $entity->disabled();

        return $entity;
    }
}
