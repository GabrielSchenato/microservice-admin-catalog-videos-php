<?php

namespace Core\UseCase\CastMember;

use Core\Domain\Entity\CastMemberEntity;
use Core\Domain\Exception\EntityValidationException;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\UseCase\DTO\CastMember\CreateCastMember\CastMemberCreateInputDto;
use Core\UseCase\DTO\CastMember\CreateCastMember\CastMemberCreateOutputDto;

class CreateCastMemberUseCase
{
    public function __construct(
        protected CastMemberRepositoryInterface $repository
    )
    {
    }

    /**
     * @throws EntityValidationException
     */
    public function execute(CastMemberCreateInputDto $input): CastMemberCreateOutputDto
    {
        $castMember = new CastMemberEntity(
            name: $input->name,
            type: $input->type,
            isActive: $input->isActive
        );

        $newCastMember = $this->repository->insert($castMember);

        return new CastMemberCreateOutputDto(
            id: $newCastMember->id(),
            name: $newCastMember->name,
            type: $newCastMember->type,
            is_active: $newCastMember->isActive,
            created_at: $newCastMember->createdAt()
        );
    }
}
