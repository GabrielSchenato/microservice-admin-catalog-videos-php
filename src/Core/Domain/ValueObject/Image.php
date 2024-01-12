<?php

namespace Core\Domain\ValueObject;

class Image
{
    public function __construct(
        protected string $path
    )
    {
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }
}
