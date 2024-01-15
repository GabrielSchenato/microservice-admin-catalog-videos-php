<?php

namespace Core\UseCase\Video\ChangeEncoded\DTO;

use Core\Domain\Enum\Rating;

class VideoChangeEncodedInputDto
{
    public function __construct(
        public string $id,
        public string $encodedPath,
    )
    {
    }
}
