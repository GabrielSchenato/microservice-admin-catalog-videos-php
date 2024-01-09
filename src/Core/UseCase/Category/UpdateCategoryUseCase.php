<?php

namespace Core\UseCase\Category;

use Core\Domain\Exception\EntityValidationException;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\DTO\Category\CategoryUpdateInputDto;
use Core\UseCase\DTO\Category\CategoryUpdateOutputDto;

class UpdateCategoryUseCase
{
    public function __construct(
        protected CategoryRepositoryInterface $repository
    )
    {
    }

    /**
     * @throws EntityValidationException
     */
    public function execute(CategoryUpdateInputDto $input): CategoryUpdateOutputDto
    {
        $category = $this->repository->findById($input->id);
        $category->update(
            name: $input->name,
            description: $input->description ?? $category->description
        );

        $categoryUpdated = $this->repository->update($category);

        return new CategoryUpdateOutputDto(
            id: $categoryUpdated->id,
            name: $categoryUpdated->name,
            description: $categoryUpdated->description,
            is_active: $categoryUpdated->isActive
        );
    }
}