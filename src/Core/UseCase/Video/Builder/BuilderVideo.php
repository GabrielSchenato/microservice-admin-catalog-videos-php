<?php

namespace Core\UseCase\Video\Builder;

use Core\Domain\Entity\AbstractEntity;
use Core\Domain\Entity\VideoEntity;
use Core\Domain\Enum\MediaStatus;
use Core\Domain\ValueObject\Image;
use Core\Domain\ValueObject\Media;

class BuilderVideo implements BuilderInterface
{
    private ?VideoEntity $entity = null;

    public function __construct()
    {
        $this->reset();
    }

    public function createEntity(object $input): void
    {
        $this->entity = new VideoEntity(
            title: $input->title,
            description: $input->description,
            yearLaunched: $input->yearLaunched,
            duration: $input->duration,
            opened: $input->opened,
            rating: $input->rating
        );
    }

    public function addMediaVideo(string $path, MediaStatus $mediaStatus): void
    {
        $media = new Media(
            filePath: $path,
            mediaStatus: $mediaStatus
        );
        $this->entity->setVideoFile($media);
    }

    public function addTrailer(string $path): void
    {
        $media = new Media(
            filePath: $path,
            mediaStatus: MediaStatus::COMPLETE
        );
        $this->entity->setTrailerFile($media);
    }

    public function addThumb(string $path): void
    {
        $this->entity->setThumbFile(new Image(
            path: $path
        ));
    }

    public function addThumbHalf(string $path): void
    {
        $this->entity->setThumbHalf(new Image(
            path: $path
        ));
    }

    public function addBanner(string $path): void
    {
        $this->entity->setBannerFile(new Image(
            path: $path
        ));
    }

    public function getEntity(): AbstractEntity
    {
        return $this->entity;
    }

    private function reset(): void
    {
        $this->entity = null;
    }
}
