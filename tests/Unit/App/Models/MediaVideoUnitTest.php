<?php

namespace App\Models;

use App\Models\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tests\Unit\App\Models\ModelTestCase;

class MediaVideoUnitTest extends ModelTestCase
{
    protected function model(): Model
    {
        return new MediaVideo();
    }


    protected function traits(): array
    {
        return [
            HasFactory::class,
            UuidTrait::class
        ];
    }

    protected function fillables(): array
    {
        return [
            'file_path',
            'encoded_path',
            'media_status',
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
