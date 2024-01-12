<?php

namespace Core\UseCase\CastMember;

use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\UseCase\DTO\CastMember\CastMemberInputDto;
use Core\UseCase\DTO\CastMember\DeleteCastMember\CastMemberDeleteOutputDto;

class DeleteCastMemberUseCase
{
    public function __construct(
        protected CastMemberRepositoryInterface $repository
    )
    {
    }

    public function execute(CastMemberInputDto $input): CastMemberDeleteOutputDto
    {
        $success = $this->repository->delete($input->id);

        return new CastMemberDeleteOutputDto(
            success: $success
        );
    }
}
