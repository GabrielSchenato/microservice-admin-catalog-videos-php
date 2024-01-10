<?php

namespace App\Repositories\Transaction;

use Core\UseCase\Interfaces\TransactionDbInterface;
use Illuminate\Support\Facades\DB;

class TransactionDb implements TransactionDbInterface
{
    public function __construct()
    {
        DB::beginTransaction();
    }

    public function commit(): void
    {
        DB::commit();
    }

    public function rollback(): void
    {
        DB::rollBack();
    }
}
