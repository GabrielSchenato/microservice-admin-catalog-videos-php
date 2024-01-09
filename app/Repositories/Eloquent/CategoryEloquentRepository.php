<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Presenters\PaginationPresenter;
use Core\Domain\Entity\CategoryEntity;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;

class CategoryEloquentRepository implements CategoryRepositoryInterface
{
    public function __construct(private Category $model)
    {
    }

    public function insert(CategoryEntity $category): CategoryEntity
    {
        $category = $this->model->create([
            'id' => $category->id,
            'name' => $category->name,
            'description' => $category->description,
            'is_active' => $category->isActive,
            'created_at' => $category->createdAt(),
        ]);

        return $this->toCategory($category);
    }

    public function findById(string $id): CategoryEntity
    {
        if (!$category = $this->model->find($id)) {
            throw new NotFoundException();
        }

        return $this->toCategory($category);
    }

    public function update(CategoryEntity $category): CategoryEntity
    {
        if (!$categoryDb = $this->model->find($category->id())) {
            throw new NotFoundException('Category Not Found');
        }

        $categoryDb->update([
            'name' => $category->name,
            'description' => $category->description,
            'is_active' => $category->isActive
        ]);

        $categoryDb->refresh();

        return $this->toCategory($categoryDb);
    }

    public function delete(string $id): bool
    {
        if (!$categoryDb = $this->model->find($id)) {
            throw new NotFoundException('Category Not Found');
        }
        return $categoryDb->delete();
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

    private function toCategory(object $object): CategoryEntity
    {
        $entity = new CategoryEntity(
            id: $object->id,
            name: $object->name,
            description: $object->description,
            createdAt: $object->created_at
        );
        $object->is_active ? $entity->activate() : $entity->disabled();

        return $entity;
    }
}
