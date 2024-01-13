<?php

namespace Core\UseCase\Video\ListPaginate;

use Core\Domain\Repository\VideoRepositoryInterface;
use Core\UseCase\Video\ListPaginate\DTO\VideosListInputDto;
use Core\UseCase\Video\ListPaginate\DTO\VideosListOutputDto;

class ListVideosUseCase
{

    public function __construct(private VideoRepositoryInterface $repository)
    {
    }

    public function execute(VideosListInputDto $input): VideosListOutputDto
    {
        $videos = $this->repository->paginate(
            filter: $input->filter,
            order: $input->order,
            page: $input->page,
            totalPage: $input->totalPage
        );

        return new VideosListOutputDto(
            items: $videos->items(),
            total: $videos->total(),
            current_page: $videos->currentPage(),
            last_page: $videos->lastPage(),
            first_page: $videos->firstPage(),
            per_page: $videos->perPage(),
            to: $videos->to(),
            from: $videos->from()
        );
    }
}
