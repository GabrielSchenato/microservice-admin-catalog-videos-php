<?php

namespace Core\UseCase\DTO\CastMember\CreateCastMember;

class CastMemberCreateInputDto
{
    public function __construct(
        public string $name,
        public int $type
    ) {
    }
}
