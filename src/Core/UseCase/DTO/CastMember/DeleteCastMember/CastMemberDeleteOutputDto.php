<?php

namespace Core\UseCase\DTO\CastMember\DeleteCastMember;

class CastMemberDeleteOutputDto
{
    public function __construct(
        public bool $success
    )
    {
    }
}
