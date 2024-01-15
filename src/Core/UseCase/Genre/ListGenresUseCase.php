<?php

namespace Core\UseCase\Genre;

use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;
use Core\UseCase\DTO\Genre\ListGenres\ListGenresInputDto;

class ListGenresUseCase
{
    public function __construct(
        protected GenreRepositoryInterface $repository
    )
    {
    }

    public function execute(ListGenresInputDto $input): PaginationInterface
    {
        return $this->repository->paginate(
            filter: $input->filter,
            order: $input->order,
            page: $input->page,
            totalPage: $input->totalPage
        );
    }
}
