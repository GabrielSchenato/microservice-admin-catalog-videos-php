<?php

namespace Tests\Feature\Core\UseCase\Video;

use Core\Domain\Enum\Rating;
use Core\UseCase\Video\Create\CreateVideoUseCase;
use Core\UseCase\Video\Create\DTO\VideoCreateInputDto;

class CreateVideoUseCaseTest extends BaseVideoUseCase
{
    public function getUseCase(): string
    {
        return CreateVideoUseCase::class;
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
        return new VideoCreateInputDto(
            title: 'test',
            description: 'test',
            yearLaunched: 2023,
            duration: 50,
            opened: true,
            rating: Rating::RATE10,
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
