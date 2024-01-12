<?php

namespace Core\UseCase\CastMember;

use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\UseCase\DTO\CastMember\ListCastMembers\ListCastMembersInputDto;
use Core\UseCase\DTO\CastMember\ListCastMembers\ListCastMembersOutputDto;

class ListCastMembersUseCase
{
    public function __construct(
        protected CastMemberRepositoryInterface $repository
    )
    {
    }

    public function execute(ListCastMembersInputDto $input): ListCastMembersOutputDto
    {
        $castMembers = $this->repository->paginate(
            filter: $input->filter,
            order: $input->order,
            page: $input->page,
            totalPage: $input->totalPage
        );

        return new ListCastMembersOutputDto(
            items: $castMembers->items(),
            total: $castMembers->total(),
            current_page: $castMembers->currentPage(),
            last_page: $castMembers->lastPage(),
            first_page: $castMembers->firstPage(),
            per_page: $castMembers->perPage(),
            to: $castMembers->to(),
            from: $castMembers->from()
        );
    }
}
