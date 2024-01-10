<?php

namespace Core\UseCase\DTO\Genre\DeleteGenre;

class GenreDeleteOutputDto
{
    public function __construct(
        public bool $success
    )
    {
    }
}
