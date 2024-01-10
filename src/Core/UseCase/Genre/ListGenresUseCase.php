<?php

namespace Core\UseCase\Genre;

use Core\Domain\Repository\GenreRepositoryInterface;
use Core\UseCase\DTO\Genre\ListGenres\ListGenresInputDto;
use Core\UseCase\DTO\Genre\ListGenres\ListGenresOutputDto;

class ListGenresUseCase
{
    public function __construct(
        protected GenreRepositoryInterface $repository
    )
    {
    }

    public function execute(ListGenresInputDto $input): ListGenresOutputDto
    {
        $genres = $this->repository->paginate(
            filter: $input->filter,
            order: $input->order,
            page: $input->page,
            totalPage: $input->totalPage
        );

        return new ListGenresOutputDto(
            items: $genres->items(),
            total: $genres->total(),
            current_page: $genres->currentPage(),
            last_page: $genres->lastPage(),
            first_page: $genres->firstPage(),
            per_page: $genres->perPage(),
            to: $genres->to(),
            from: $genres->from()
        );
    }
}
