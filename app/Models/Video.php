<?php

namespace App\Models;

use App\Enums\ImageTypes;
use App\Enums\MediaTypes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = false;

    protected $fillable = [
        'id',
        'title',
        'description',
        'year_launched',
        'opened',
        'rating',
        'duration',
        'created_at',
    ];

    protected $casts = [
        'id' => 'string',
        'deleted_at' => 'datetime',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class);
    }

    public function castMembers(): BelongsToMany
    {
        return $this->belongsToMany(CastMember::class);
    }

    public function media(): HasOne
    {
        return $this
            ->hasOne(MediaVideo::class)
            ->whereType((string)MediaTypes::VIDEO->value);
    }

    public function trailer(): HasOne
    {
        return $this
            ->hasOne(MediaVideo::class)
            ->whereType((string)MediaTypes::TRAILER->value);
    }

    public function banner()
    {
        return $this
            ->hasOne(ImageVideo::class)
            ->whereType((string)ImageTypes::BANNER->value);
    }

    public function thumb()
    {
        return $this
            ->hasOne(ImageVideo::class)
            ->whereType((string)ImageTypes::THUMB->value);
    }

    public function thumbHalf()
    {
        return $this
            ->hasOne(ImageVideo::class)
            ->whereType((string)ImageTypes::THUMB_HALF->value);
    }
}
