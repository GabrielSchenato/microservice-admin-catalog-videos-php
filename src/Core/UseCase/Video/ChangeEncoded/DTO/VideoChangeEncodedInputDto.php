<?php

namespace Core\UseCase\Video\ChangeEncoded\DTO;

class VideoChangeEncodedInputDto
{
    public function __construct(
        public string $id,
        public string $encodedPath,
    ) {
    }
}
