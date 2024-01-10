<?php

namespace Core\UseCase\Interfaces;

interface TransactionDbInterface
{
    public function commit(): void;

    public function rollback(): void;
}
