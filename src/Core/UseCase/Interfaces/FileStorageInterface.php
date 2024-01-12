<?php

namespace Core\UseCase\Interfaces;

interface FileStorageInterface
{
    public function store(string $path, array $file): string;

    public function delete(string $path): bool;
}
