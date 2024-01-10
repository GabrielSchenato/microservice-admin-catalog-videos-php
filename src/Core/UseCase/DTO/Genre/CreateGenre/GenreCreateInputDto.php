<?php

namespace Core\UseCase\DTO\Genre\CreateGenre;

class GenreCreateInputDto
{
    public function __construct(
        public string $name,
        public array  $categoriesId = [],
        public bool   $isActive = true
    )
    {
    }
}
