<?php

namespace Core\UseCase\Video\Update\DTO;

class VideoUpdateInputDto
{
    public function __construct(
        public string $id,
        public string $title,
        public string $description,
        public array  $categoriesId,
        public array  $genresId,
        public array  $castMembersId,
        public ?array $videoFile = null,
        public ?array $trailerFile = null,
        public ?array $thumbFile = null,
        public ?array $thumbHalf = null,
        public ?array $bannerFile = null,
    )
    {
    }
}
