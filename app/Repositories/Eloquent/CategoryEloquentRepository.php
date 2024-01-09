<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Presenters\PaginationPresenter;
use Core\Domain\Entity\CategoryEntity;
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
        return new CategoryEntity(
            name: '$object->name'
        );
    }

    public function update(CategoryEntity $category): CategoryEntity
    {
        return new CategoryEntity(
            name: 'testeee'
        );
    }

    public function delete(string $id): bool
    {
        return true;
    }

    public function findAll(string $filter = '', $order = 'DESC'): array
    {
        return [];
    }

    public function paginate(string $filter = '', $order = 'DESC', int $page = 1, int $totalPage = 15): PaginationInterface
    {
        return new PaginationPresenter();
    }

    private function toCategory(object $object): CategoryEntity
    {
        return new CategoryEntity(
            name: $object->name
        );
    }
}
