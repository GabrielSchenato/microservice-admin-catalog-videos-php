<?php

namespace Core\UseCase\Video\ListPaginate;

use Core\Domain\Repository\PaginationInterface;
use Core\Domain\Repository\VideoRepositoryInterface;
use Core\UseCase\Video\ListPaginate\DTO\VideosListInputDto;

class ListVideosUseCase
{
    public function __construct(private VideoRepositoryInterface $repository)
    {
    }

    public function execute(VideosListInputDto $input): PaginationInterface
    {
        return $this->repository->paginate(
            filter: $input->filter,
            order: $input->order,
            page: $input->page,
            totalPage: $input->totalPage
        );
    }
}
