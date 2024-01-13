<?php

namespace Core\UseCase\Video\Builder;

use Core\Domain\Entity\AbstractEntity;
use Core\Domain\Enum\MediaStatus;

interface BuilderInterface
{
    public function createEntity(object $input): void;

    public function addMediaVideo(string $path, MediaStatus $mediaStatus): void;

    public function addTrailer(string $path): void;

    public function addThumb(string $path): void;

    public function addThumbHalf(string $path): void;

    public function addBanner(string $path): void;

    public function getEntity(): AbstractEntity;
}