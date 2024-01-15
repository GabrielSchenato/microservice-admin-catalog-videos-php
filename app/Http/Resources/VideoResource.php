<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'year_launched' => $this->year_launched ?? $this->yearLaunched,
            'opened' => $this->opened,
            'rating' => $this->rating,
            'duration' => $this->duration,
            'created_at' => $this->created_at ?? $this->createdAt,
            'video' => $this->videoFile ?? '',
            'trailer' => $this->trailerFile ?? '',
            'banner' => $this->bannerFile ?? '',
            'thumb' => $this->thumbFile ?? '',
            'thumb_half' => $this->thumbHalfFile ?? '',
            'categories' => $this->categories,
            'genres' => $this->genres,
            'cast_members' => $this->cast_members ?? $this->castMembers ?? [],
        ];
    }
}
