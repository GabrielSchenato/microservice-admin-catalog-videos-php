<?php

namespace Tests\Unit\UseCase\Video;

use Core\Domain\Repository\VideoRepositoryInterface;
use Core\Domain\ValueObject\Uuid;
use Core\UseCase\Video\Delete\DeleteVideoUseCase;
use Core\UseCase\Video\Delete\DTO\VideoDeleteInputDto;
use Core\UseCase\Video\Delete\DTO\VideoDeleteOutputDto;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;

class DeleteVideoUseCaseUnitTest extends TestCase
{
    public function testDelete()
    {
        $uuid = Uuid::random();

        $useCase = new DeleteVideoUseCase($this->getMockRepository());

        $response = $useCase->execute(
            input: $this->getMockInputDTO($uuid)
        );

        $this->assertInstanceOf(VideoDeleteOutputDto::class, $response);
    }

    private function getMockRepository()
    {
        $mocRepository = Mockery::mock(stdClass::class, VideoRepositoryInterface::class);
        $mocRepository
            ->shouldReceive('delete')
            ->once()
            ->andReturn(true);

        return $mocRepository;
    }

    private function getMockInputDto(string $id)
    {
        return Mockery::mock(VideoDeleteInputDto::class, [
            $id,
        ]);
    }
}
