<?php

namespace Tests\Unit\UseCase\Video;

use Core\Domain\Enum\Rating;
use Core\Domain\ValueObject\Uuid;
use Core\UseCase\Video\Update\UpdateVideoUseCase;
use Core\UseCase\Video\Update\DTO\VideoUpdateInputDto;
use Core\UseCase\Video\Update\DTO\VideoUpdateOutputDto;
use Mockery;

class UpdateVideoUseCaseUnit extends BaseVideoUseCaseUnit
{

    public function testExecuteInputOutput()
    {
        $this->createUseCase();

        $response = $this->useCase->execute(
            input: $this->createMockInputDto()
        );

        $this->assertInstanceOf(VideoUpdateOutputDto::class, $response);
    }

    protected function createMockInputDto(
        array  $categoriesId = [],
        array  $genresId = [],
        array  $castMembersId = [],
        ?array $videoFile = null,
        ?array $trailerFile = null,
        ?array $thumbFile = null,
        ?array $thumbHalf = null,
        ?array $bannerFile = null,
    )
    {
        return Mockery::mock(VideoUpdateInputDto::class, [
            Uuid::random(),
            'title',
            'desc',
            2023,
            12,
            true,
            Rating::RATE12,
            $categoriesId,
            $genresId,
            $castMembersId,
            $videoFile,
            $trailerFile,
            $thumbFile,
            $thumbHalf,
            $bannerFile,
        ]);
    }

    protected function getNameActionRepository(): string
    {
        return 'update';
    }

    protected function getUseCase(): string
    {
        return UpdateVideoUseCase::class;
    }
}
