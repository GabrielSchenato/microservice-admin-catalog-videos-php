<?php

namespace Core\UseCase\DTO\CastMember\CreateCastMember;

class CastMemberCreateOutputDto
{
    public function __construct(
        public string $id,
        public string $name,
        public int    $type,
        public bool   $is_active = true,
        public string $created_at = ''
    )
    {
    }
}
