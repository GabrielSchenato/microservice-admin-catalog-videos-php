<?php

namespace Core\UseCase\Video\Delete\DTO;

class VideoDeleteInputDto
{
    public function __construct(
        public string $id
    )
    {
    }
}
