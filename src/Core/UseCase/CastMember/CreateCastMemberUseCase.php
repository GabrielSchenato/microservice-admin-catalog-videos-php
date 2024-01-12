<?php

namespace Core\UseCase\CastMember;

use Core\Domain\Entity\CastMemberEntity;
use Core\Domain\Enum\CastMemberType;
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
            type: $input->type == 1 ? CastMemberType::DIRECTOR : CastMemberType::ACTOR
        );

        $newCastMember = $this->repository->insert($castMember);

        return new CastMemberCreateOutputDto(
            id: $newCastMember->id(),
            name: $newCastMember->name,
            type: $input->type,
            created_at: $newCastMember->createdAt()
        );
    }
}
