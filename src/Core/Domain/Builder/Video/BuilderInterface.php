<?php

namespace Core\Domain\Builder\Video;

use Core\Domain\Entity\VideoEntity;
use Core\Domain\Enum\MediaStatus;

interface BuilderInterface
{
    public function createEntity(object $input): self;

    public function addMediaVideo(string $path, MediaStatus $mediaStatus, string $encodedPath = ''): self;

    public function addTrailer(string $path): self;

    public function addThumb(string $path): self;

    public function addThumbHalf(string $path): self;

    public function addBanner(string $path): self;

    public function getEntity(): VideoEntity;
}
