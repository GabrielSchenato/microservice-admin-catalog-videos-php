<?php

namespace Core\UseCase\CastMember;

use Core\Domain\Exception\EntityValidationException;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\UseCase\DTO\CastMember\UpdateCastMember\CastMemberUpdateInputDto;
use Core\UseCase\DTO\CastMember\UpdateCastMember\CastMemberUpdateOutputDto;

class UpdateCastMemberUseCase
{
    public function __construct(
        protected CastMemberRepositoryInterface $repository
    ) {
    }

    /**
     * @throws EntityValidationException
     */
    public function execute(CastMemberUpdateInputDto $input): CastMemberUpdateOutputDto
    {
        $castMember = $this->repository->findById($input->id);
        $castMember->update(
            name: $input->name
        );

        $castMemberUpdated = $this->repository->update($castMember);

        return new CastMemberUpdateOutputDto(
            id: $castMemberUpdated->id,
            name: $castMemberUpdated->name,
            type: $castMemberUpdated->type->value,
            created_at: $castMemberUpdated->createdAt()
        );
    }
}
