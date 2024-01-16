<?php

namespace Core\UseCase\CastMember;

use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;
use Core\UseCase\DTO\CastMember\ListCastMembers\ListCastMembersInputDto;

class ListCastMembersUseCase
{
    public function __construct(
        protected CastMemberRepositoryInterface $repository
    ) {
    }

    public function execute(ListCastMembersInputDto $input): PaginationInterface
    {
        return $this->repository->paginate(
            filter: $input->filter,
            order: $input->order,
            page: $input->page,
            totalPage: $input->totalPage
        );
    }
}
