<?php

namespace Tests\Unit\UseCase\Video;

use Core\Domain\Enum\Rating;
use Core\UseCase\Video\Create\CreateVideoUseCase;
use Core\UseCase\Video\Create\DTO\VideoCreateInputDto;
use Core\UseCase\Video\Create\DTO\VideoCreateOutputDto;
use Mockery;

class CreateVideoUseCaseUnit extends BaseVideoUseCaseUnit
{

    public function testExecuteInputOutput()
    {
        $this->createUseCase();

        $response = $this->useCase->execute(
            input: $this->createMockInputDto()
        );

        $this->assertInstanceOf(VideoCreateOutputDto::class, $response);
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
        return Mockery::mock(VideoCreateInputDto::class, [
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
        return 'insert';
    }

    protected function getUseCase(): string
    {
        return CreateVideoUseCase::class;
    }
}
