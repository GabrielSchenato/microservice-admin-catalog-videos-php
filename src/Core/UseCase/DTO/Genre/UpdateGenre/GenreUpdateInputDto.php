<?php

namespace Core\UseCase\DTO\Genre\UpdateGenre;

class GenreUpdateInputDto
{
    public function __construct(
        public string $id,
        public string $name,
        public array  $categoriesId = [],
        public bool   $isActive = true
    )
    {
    }
}
