<?php

namespace App\Services\Storage;

use Core\UseCase\Interfaces\FileStorageInterface;

class FileStorage implements FileStorageInterface
{

    public function store(string $path, array $file): string
    {
        return '';
    }

    public function delete(string $path): bool
    {
        return true;
    }
}
