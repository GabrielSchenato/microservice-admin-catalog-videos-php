<?php

namespace Core\UseCase\Category;

use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\DTO\Category\CategoryDeleteOutputDto;
use Core\UseCase\DTO\Category\CategoryInputDto;

class DeleteCategoryUseCase
{
    public function __construct(
        protected CategoryRepositoryInterface $repository
    )
    {
    }

    public function execute(CategoryInputDto $input): CategoryDeleteOutputDto
    {
        $success = $this->repository->delete($input->id);

        return new CategoryDeleteOutputDto(
            success: $success
        );
    }
}