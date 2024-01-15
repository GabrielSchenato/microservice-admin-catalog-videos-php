<?php

namespace Core\UseCase\Category;

use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;
use Core\UseCase\DTO\Category\ListCategories\ListCategoriesInputDto;

class ListCategoriesUseCase
{
    public function __construct(
        protected CategoryRepositoryInterface $repository
    )
    {
    }

    public function execute(ListCategoriesInputDto $input): PaginationInterface
    {
        return $this->repository->paginate(
            filter: $input->filter,
            order: $input->order,
            page: $input->page,
            totalPage: $input->totalPage
        );
    }
}
