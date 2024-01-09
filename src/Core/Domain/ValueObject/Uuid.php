<?php

namespace Core\Domain\ValueObject;

use InvalidArgumentException;

class Uuid
{
    public function __construct(
        protected string $value
    )
    {
        $this->ensureIsValid();
    }

    private function ensureIsValid(): void
    {
        if (!\Ramsey\Uuid\Uuid::isValid($this->value)) {
            throw new InvalidArgumentException(sprintf('<%s> does not allow the value <%s>.', static::class, $this->value));
        }
    }

    public static function random(): self
    {
        return new self(\Ramsey\Uuid\Uuid::uuid4()->toString());
    }

    public function __toString(): string
    {
        return $this->value;
    }
}