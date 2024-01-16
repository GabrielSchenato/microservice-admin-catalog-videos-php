<?php

namespace Tests\Unit\UseCase\Video;

use Core\Domain\Entity\VideoEntity;
use Core\Domain\Enum\Rating;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\VideoRepositoryInterface;
use Core\UseCase\Video\ChangeEncoded\ChangeEncodedPathVideoUseCase;
use Core\UseCase\Video\ChangeEncoded\DTO\VideoChangeEncodedInputDto;
use Core\UseCase\Video\ChangeEncoded\DTO\VideoChangeEncodedOutputDto;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;

class ChangeEncodedPathVideoUseCaseUnitTest extends TestCase
{
    public function testSpies()
    {
        $input = new VideoChangeEncodedInputDto(
            id: 'id-video',
            encodedPath: 'path/video_encoded.ext',
        );

        $mockRepository = Mockery::mock(stdClass::class, VideoRepositoryInterface::class);
        $mockRepository->shouldReceive('findById')
            ->times(1)
            ->with($input->id)
            ->andReturn($this->getEntity());
        $mockRepository->shouldReceive('updateMedia')
            ->times(1);

        $useCase = new ChangeEncodedPathVideoUseCase(
            repository: $mockRepository
        );

        $response = $useCase->execute(input: $input);

        $this->assertInstanceOf(VideoChangeEncodedOutputDto::class, $response);

        Mockery::close();
    }

    public function testExceptionRepository()
    {
        $this->expectException(NotFoundException::class);

        $input = new VideoChangeEncodedInputDto(
            id: 'id-video',
            encodedPath: 'path/video_encoded.ext',
        );

        $mockRepository = Mockery::mock(stdClass::class, VideoRepositoryInterface::class);
        $mockRepository->shouldReceive('findById')
            ->times(1)
            ->with($input->id)
            ->andThrow(new NotFoundException('Not Found Video'));
        $mockRepository->shouldReceive('updateMedia')
            ->times(0);

        $useCase = new ChangeEncodedPathVideoUseCase(
            repository: $mockRepository
        );

        $useCase->execute(input: $input);

        Mockery::close();
    }

    private function getEntity(): VideoEntity
    {
        return new VideoEntity(
            title: 'title',
            description: 'desc',
            yearLaunched: 2026,
            duration: 1,
            opened: true,
            rating: Rating::L
        );
    }
}
