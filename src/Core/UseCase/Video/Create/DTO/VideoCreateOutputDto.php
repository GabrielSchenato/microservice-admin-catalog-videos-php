<?php

namespace Core\UseCase\Video\Create\DTO;

use Core\Domain\Enum\Rating;

class VideoCreateOutputDto
{
    public function __construct(
        public string     $id,
        public string     $title,
        public string     $description,
        public int        $yearLaunched,
        public int        $duration,
        public bool       $opened,
        public Rating     $rating,
        protected array   $categoriesId = [],
        protected array   $genresId = [],
        protected array   $castMembersId = [],
        protected ?string $thumbFile = null,
        protected ?string $thumbHalf = null,
        protected ?string $bannerFile = null,
        protected ?string $trailerFile = null,
        protected ?string $videoFile = null,
    )
    {
    }
}
