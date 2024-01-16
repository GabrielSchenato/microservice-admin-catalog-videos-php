<?php

namespace Tests\Unit\UseCase\Video;

use Core\Domain\Entity\VideoEntity;
use Core\Domain\Enum\Rating;
use Core\Domain\Repository\VideoRepositoryInterface;
use Core\Domain\ValueObject\Uuid;
use Core\UseCase\Video\List\DTO\VideoListInputDto;
use Core\UseCase\Video\List\DTO\VideoListOutputDto;
use Core\UseCase\Video\List\ListVideoUseCase;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;

class ListVideoUseCaseUnitTest extends TestCase
{
    public function testList()
    {
        $uuid = Uuid::random();

        $useCase = new ListVideoUseCase($this->getMockRepository());

        $response = $useCase->execute(
            input: $this->getMockInputDTO($uuid)
        );

        $this->assertInstanceOf(VideoListOutputDto::class, $response);
    }

    private function getMockRepository()
    {
        $mocRepository = Mockery::mock(stdClass::class, VideoRepositoryInterface::class);
        $mocRepository
            ->shouldReceive('findById')
            ->once()
            ->andReturn($this->getEntity());

        return $mocRepository;
    }

    private function getEntity(): VideoEntity
    {
        return new VideoEntity(
            title: 'title',
            description: 'desc',
            yearLaunched: 2023,
            duration: 12,
            opened: true,
            rating: Rating::RATE12
        );
    }

    private function getMockInputDto(string $id)
    {
        return Mockery::mock(VideoListInputDto::class, [
            $id,
        ]);
    }
}
