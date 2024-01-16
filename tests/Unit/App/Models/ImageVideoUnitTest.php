<?php

namespace App\Models;

use App\Models\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tests\Unit\App\Models\ModelTestCase;

class ImageVideoUnitTest extends ModelTestCase
{
    protected function model(): Model
    {
        return new ImageVideo();
    }

    protected function traits(): array
    {
        return [
            HasFactory::class,
            UuidTrait::class,
        ];
    }

    protected function fillables(): array
    {
        return [
            'path',
            'type',
        ];
    }

    protected function casts(): array
    {
        return [
            'id' => 'string',
        ];
    }
}
