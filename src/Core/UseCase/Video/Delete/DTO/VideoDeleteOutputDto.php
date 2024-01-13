<?php

namespace Core\UseCase\Video\Delete\DTO;

class VideoDeleteOutputDto
{
    public function __construct(
        public bool $success
    )
    {
    }
}
