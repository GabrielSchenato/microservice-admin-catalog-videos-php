<?php

namespace Core\Domain\Validation;

use Core\Domain\Entity\AbstractEntity;

interface ValidatorInterface
{
    public function validate(AbstractEntity $entity): void;
}
