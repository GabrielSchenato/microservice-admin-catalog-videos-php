<?php

namespace Core\Domain\Repository;

use Core\Domain\Entity\AbstractEntity;

interface VideoRepositoryInterface extends EntityRepositoryInterface
{
    public function updateMedia(AbstractEntity $entity): AbstractEntity;
}
