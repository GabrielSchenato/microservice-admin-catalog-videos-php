<?php

namespace Core\UseCase\Category;

use Core\Domain\Entity\CategoryEntity;
use Core\Domain\Exception\EntityValidationException;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\DTO\Category\CreateCategory\CategoryCreateInputDto;
use Core\UseCase\DTO\Category\CreateCategory\CategoryCreateOutputDto;

class CreateCategoryUseCase
{
    public function __construct(
        protected CategoryRepositoryInterface $repository
    )
    {
    }

    /**
     * @throws EntityValidationException
     */
    public function execute(CategoryCreateInputDto $input): CategoryCreateOutputDto
    {
        $category = new CategoryEntity(
            name: $input->name,
            description: $input->description,
            isActive: $input->isActive
        );

        $newCategory = $this->repository->insert($category);

        return new CategoryCreateOutputDto(
            id: $newCategory->id(),
            name: $newCategory->name,
            description: $newCategory->description,
            is_active: $newCategory->isActive,
            created_at: $newCategory->createdAt()
        );
    }
}