<?php

namespace Core\Domain\Entity\Traits;

use Exception;

trait MethodsMagicsTrait
{
    /**
     * @throws Exception
     */
    public function __get(string $name)
    {
        if (isset($this->{$name}))
            return $this->{$name};

        $className = get_class($this);
        throw new Exception("Property {$name} not found in class {$className}");
    }
}