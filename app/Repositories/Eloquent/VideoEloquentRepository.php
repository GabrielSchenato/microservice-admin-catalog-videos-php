<?php

namespace App\Repositories\Eloquent;

use App\Models\Video;
use App\Repositories\Presenters\PaginationPresenter;
use Core\Domain\Entity\AbstractEntity;
use Core\Domain\Entity\VideoEntity;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\PaginationInterface;
use Core\Domain\Repository\VideoRepositoryInterface;

class VideoEloquentRepository implements VideoRepositoryInterface
{
    public function __construct(private Video $model)
    {
    }

    public function insert(AbstractEntity $entity): VideoEntity
    {
        $entity = $this->model->create([
            'id' => $entity->id,
            'name' => $entity->name,
            'description' => $entity->description,
            'is_active' => $entity->isActive,
            'created_at' => $entity->createdAt(),
        ]);

        return $this->toVideo($entity);
    }

    public function findById(string $id): VideoEntity
    {
        if (!$video = $this->model->find($id)) {
            throw new NotFoundException();
        }

        return $this->toVideo($video);
    }

    public function getIdsListIds(array $categoriesId = []): array
    {
        return $this->model
            ->whereIn('id', $categoriesId)
            ->pluck('id')
            ->toArray();
    }

    public function update(AbstractEntity $entity): VideoEntity
    {
        if (!$videoDb = $this->model->find($entity->id())) {
            throw new NotFoundException('Video Not Found');
        }

        $videoDb->update([
            'name' => $entity->name,
            'description' => $entity->description,
            'is_active' => $entity->isActive
        ]);

        $videoDb->refresh();

        return $this->toVideo($videoDb);
    }

    public function delete(string $id): bool
    {
        if (!$videoDb = $this->model->find($id)) {
            throw new NotFoundException('Video Not Found');
        }
        return $videoDb->delete();
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
            ->paginate($totalPage);

        return new PaginationPresenter($paginator);
    }

    private function toVideo(object $object): VideoEntity
    {
        $entity = new VideoEntity(
            id: $object->id,
            name: $object->name,
            description: $object->description,
            createdAt: $object->created_at
        );
        $object->is_active ? $entity->activate() : $entity->disabled();

        return $entity;
    }

    public function updateMedia(AbstractEntity $entity): AbstractEntity
    {
        // TODO: Implement updateMedia() method.
    }
}
