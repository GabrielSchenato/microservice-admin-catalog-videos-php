<?php

namespace Core\UseCase\Video\Create\DTO;

use Core\Domain\Enum\Rating;

class VideoCreateInputDto
{
    public function __construct(
        public string $title,
        public string $description,
        public int $yearLaunched,
        public int $duration,
        public bool $opened,
        public Rating $rating,
        public array $categoriesId,
        public array $genresId,
        public array $castMembersId,
        public ?array $videoFile = null,
        public ?array $trailerFile = null,
        public ?array $thumbFile = null,
        public ?array $thumbHalf = null,
        public ?array $bannerFile = null,
    ) {
    }
}
