<?php

namespace App\Models;

use App\Models\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImageVideo extends Model
{
    use HasFactory, UuidTrait;

    public $incrementing = false;

    protected $fillable = [
        'path',
        'type',
    ];

    protected $casts = [
        'id' => 'string',
    ];

    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class);
    }
}
