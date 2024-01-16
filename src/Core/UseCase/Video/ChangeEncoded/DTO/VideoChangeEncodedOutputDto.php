<?php

namespace Core\UseCase\Video\ChangeEncoded\DTO;

class VideoChangeEncodedOutputDto
{
    public function __construct(
        public string $id,
        public string $encodedPath,
    ) {
    }
}
