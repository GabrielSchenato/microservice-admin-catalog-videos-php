<?php

namespace Core\Domain\Entity;

use Core\Domain\Entity\Traits\MethodsMagicsTrait;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Exception\EntityValidationException;
use Core\Domain\Validation\DomainValidation;
use Core\Domain\ValueObject\Uuid;
use DateTime;
use Exception;

class CastMemberEntity
{
    use MethodsMagicsTrait;

    /**
     * @throws EntityValidationException
     * @throws Exception
     */
    public function __construct(
        protected string         $name,
        protected CastMemberType $type,
        protected ?Uuid          $id = null,
        protected bool           $isActive = true,
        protected ?DateTime      $createdAt = null
    )
    {
        $this->id = $this->id ?? Uuid::random();
        $this->createdAt = $this->createdAt ?? new DateTime();

        $this->validate();
    }

    /**
     * @throws EntityValidationException
     */
    private function validate(): void
    {
        DomainValidation::strMaxLength($this->name);
        DomainValidation::strMinLength($this->name);
    }

    public function activate(): void
    {
        $this->isActive = true;
    }

    public function disabled(): void
    {
        $this->isActive = false;
    }

    /**
     * @throws EntityValidationException
     */
    public function update(string $name): void
    {
        $this->name = $name;

        $this->validate();
    }
}
