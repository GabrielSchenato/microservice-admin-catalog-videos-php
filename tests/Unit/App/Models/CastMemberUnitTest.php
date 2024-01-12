<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tests\Unit\App\Models\ModelTestCase;

class CastMemberUnitTest extends ModelTestCase
{
    protected function model(): Model
    {
        return new CastMember();
    }


    protected function traits(): array
    {
        return [
            HasFactory::class,
            SoftDeletes::class
        ];
    }

    protected function fillables(): array
    {
        return [
            'id',
            'name',
            'type',
        ];
    }

    protected function casts(): array
    {
        return [
            'id' => 'string',
            'deleted_at' => 'datetime'
        ];
    }
}
