<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CastMember extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'type',
    ];

    protected $casts = [
        'id' => 'string',
        'deleted_at' => 'datetime',
    ];

    public function videos(): BelongsToMany
    {
        return $this->belongsToMany(Video::class);
    }
}
