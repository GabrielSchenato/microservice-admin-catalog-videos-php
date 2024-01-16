<?php

namespace Tests\Feature\Core\UseCase\Video;

use App\Models\Video;
use Core\UseCase\Video\Update\DTO\VideoUpdateInputDto;
use Core\UseCase\Video\Update\UpdateVideoUseCase;

class UpdateVideoUseCaseTest extends BaseVideoUseCase
{
    public function getUseCase(): string
    {
        return UpdateVideoUseCase::class;
    }

    public function inputDTO(array $categories = [],
        array $genres = [],
        array $castMembers = [],
        ?array $videoFile = null,
        ?array $trailerFile = null,
        ?array $bannerFile = null,
        ?array $thumbFile = null,
        ?array $thumbHalf = null,
    ): object {
        $video = Video::factory()->create();

        return new VideoUpdateInputDto(
            id: $video->id,
            title: 'test',
            description: 'test',
            categoriesId: $categories,
            genresId: $genres,
            castMembersId: $castMembers,
            videoFile: $videoFile,
            trailerFile: $trailerFile,
            thumbFile: $bannerFile,
            thumbHalf: $thumbFile,
            bannerFile: $thumbHalf,
        );
    }
}
