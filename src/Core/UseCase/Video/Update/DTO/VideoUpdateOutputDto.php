<?php

namespace Core\UseCase\Video\Update\DTO;

use Core\Domain\Enum\Rating;

class VideoUpdateOutputDto
{
    public function __construct(
        public string     $id,
        public string     $title,
        public string     $description,
        public int        $yearLaunched,
        public int        $duration,
        public bool       $opened,
        public Rating     $rating,
        public array   $categoriesId = [],
        public array   $genresId = [],
        public array   $castMembersId = [],
        public ?string $thumbFile = null,
        public ?string $thumbHalf = null,
        public ?string $bannerFile = null,
        public ?string $trailerFile = null,
        public ?string $videoFile = null,
    )
    {
    }
}
