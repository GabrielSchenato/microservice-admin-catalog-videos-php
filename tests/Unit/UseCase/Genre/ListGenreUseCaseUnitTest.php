<?php

namespace Tests\Unit\UseCase\Genre;

use Core\Domain\Entity\GenreEntity;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\ValueObject\Uuid;
use Core\UseCase\DTO\Genre\GenreInputDto;
use Core\UseCase\DTO\Genre\GenreOutputDto;
use Core\UseCase\Genre\ListGenreUseCase;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid as RamseyUuid;
use stdClass;

class ListGenreUseCaseUnitTest extends TestCase
{
    public function testGetById()
    {
        $uuid = new Uuid(RamseyUuid::uuid4()->toString());
        $genreName = 'New Genre';
        $mockEntity = Mockery::mock(GenreEntity::class, [
            $genreName,
            $uuid,
            true,
            []
        ]);
        $mockEntity->shouldReceive('id')->andReturn($uuid);
        $mockEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));

        $mockRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockRepository
            ->shouldReceive('findById')
            ->once()
            ->with($uuid->__toString())
            ->andReturn($mockEntity);

        $mockInputDto = Mockery::mock(GenreInputDto::class, [
            $uuid
        ]);

        $useCase = new ListGenreUseCase($mockRepository);
        $response = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(GenreOutputDto::class, $response);
        $this->assertEquals($genreName, $response->name);
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
