<?php

namespace Core\Domain\Builder\Video;

use Core\Domain\Entity\VideoEntity;
use Core\Domain\ValueObject\Uuid;
use DateTime;

class UpdateBuilderVideo extends CreateBuilderVideo
{

    public function createEntity(object $input): self
    {
        $this->entity = new VideoEntity(
            title: $input->title,
            description: $input->description,
            yearLaunched: $input->yearLaunched,
            duration: $input->duration,
            opened: $input->opened,
            rating: $input->rating,
            id: new Uuid($input->id),
            createdAt: new DateTime($input->createdAt)
        );

        $this->addIds($input);

        return $this;
    }

    public function setEntity(VideoEntity $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}
