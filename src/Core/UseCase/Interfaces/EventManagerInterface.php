<?php

namespace Core\UseCase\Interfaces;

interface EventManagerInterface
{
    public function dispatch(object $data): void;
}
