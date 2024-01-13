<?php

namespace Core\Domain\Builder\Video;

use Core\Domain\Entity\AbstractEntity;
use Core\Domain\Entity\VideoEntity;
use Core\Domain\Enum\MediaStatus;
use Core\Domain\ValueObject\Image;
use Core\Domain\ValueObject\Media;

class CreateBuilderVideo implements BuilderInterface
{
    protected ?VideoEntity $entity = null;

    public function __construct()
    {
        $this->reset();
    }

    public function createEntity(object $input): self
    {
        $this->entity = new VideoEntity(
            title: $input->title,
            description: $input->description,
            yearLaunched: $input->yearLaunched,
            duration: $input->duration,
            opened: $input->opened,
            rating: $input->rating
        );

        $this->addIds($input);

        return $this;
    }

    protected function addIds(object $input)
    {
        foreach ($input->categoriesId as $categoryId) {
            $this->entity->addCategory($categoryId);
        }
        foreach ($input->genresId as $genreId) {
            $this->entity->addGenre($genreId);
        }
        foreach ($input->castMembersId as $castMemberId) {
            $this->entity->addCastMember($castMemberId);
        }
    }

    public function addMediaVideo(string $path, MediaStatus $mediaStatus): self
    {
        $media = new Media(
            filePath: $path,
            mediaStatus: $mediaStatus
        );
        $this->entity->setVideoFile($media);

        return $this;
    }

    public function addTrailer(string $path): self
    {
        $media = new Media(
            filePath: $path,
            mediaStatus: MediaStatus::COMPLETE
        );
        $this->entity->setTrailerFile($media);

        return $this;
    }

    public function addThumb(string $path): self
    {
        $this->entity->setThumbFile(new Image(
            path: $path
        ));

        return $this;
    }

    public function addThumbHalf(string $path): self
    {
        $this->entity->setThumbHalf(new Image(
            path: $path
        ));

        return $this;
    }

    public function addBanner(string $path): self
    {
        $this->entity->setBannerFile(new Image(
            path: $path
        ));

        return $this;
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
