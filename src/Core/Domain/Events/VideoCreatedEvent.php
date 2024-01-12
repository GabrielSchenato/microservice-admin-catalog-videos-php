<?php

namespace Core\Domain\Events;

use Core\Domain\Entity\VideoEntity;

class VideoCreatedEvent implements EventInterface
{
    public function __construct(protected VideoEntity $entity)
    {
    }

    public function getEventName(): string
    {
        return 'video.created';
    }

    public function getPayload(): array
    {
        return [
            'resource_id' => $this->entity->id(),
            'file_path' => $this->entity->getVideoFile()->path
        ];
    }
}
