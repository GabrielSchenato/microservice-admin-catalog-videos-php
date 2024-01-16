<?php

namespace Tests\Unit\UseCase\Genre;

use Core\Domain\Entity\GenreEntity;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\ValueObject\Uuid;
use Core\UseCase\DTO\Genre\CreateGenre\GenreCreateInputDto;
use Core\UseCase\DTO\Genre\CreateGenre\GenreCreateOutputDto;
use Core\UseCase\Genre\CreateGenreUseCase;
use Core\UseCase\Interfaces\TransactionDbInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid as RamseyUuid;
use stdClass;

class CreateGenreUseCaseUnitTest extends TestCase
{
    public function testCreateNewGenre()
    {
        $uuid = new Uuid(RamseyUuid::uuid4()->toString());
        $genreName = 'New Genre';
        $mockEntity = $this->getMockEntity($genreName, $uuid);

        $mockRepository = $this->getMockRepository($mockEntity);

        $mockTransactionDb = $this->getMockTransactionDb();
        $mockCategoryRepository = $this->getMockCategoryRepository();
        $mockCategoryRepository->shouldReceive('getIdsListIds')->andReturn([
            $uuid->__toString(),
        ]);

        $mockInputDto = Mockery::mock(GenreCreateInputDto::class, [
            $genreName,
            [$uuid->__toString()],
        ]);

        $useCase = new CreateGenreUseCase($mockRepository, $mockTransactionDb, $mockCategoryRepository);
        $response = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(GenreCreateOutputDto::class, $response);
        $this->assertEquals($genreName, $response->name);
    }

    public function testCreateNewGenreCategoryNotFound()
    {
        $this->expectException(NotFoundException::class);

        $uuid = new Uuid(RamseyUuid::uuid4()->toString());
        $genreName = 'New Genre';
        $mockEntity = $this->getMockEntity($genreName, $uuid);

        $mockRepository = $this->getMockRepository($mockEntity, 0);

        $mockTransactionDb = $this->getMockTransactionDb();
        $mockCategoryRepository = $this->getMockCategoryRepository();
        $mockCategoryRepository->shouldReceive('getIdsListIds');

        $mockInputDto = Mockery::mock(GenreCreateInputDto::class, [
            $genreName,
            [$uuid->__toString()],
        ]);

        $useCase = new CreateGenreUseCase($mockRepository, $mockTransactionDb, $mockCategoryRepository);
        $useCase->execute($mockInputDto);
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    protected function getMockEntity(string $genreName, Uuid $uuid)
    {
        $mockEntity = Mockery::mock(GenreEntity::class, [
            $genreName,
            $uuid,
        ]);
        $mockEntity->shouldReceive('id')->andReturn($uuid);
        $mockEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));

        return $mockEntity;
    }

    protected function getMockRepository($mockEntity, int $times = 1)
    {
        $mockRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockRepository
            ->shouldReceive('insert')
            ->times($times)
            ->andReturn($mockEntity);

        return $mockRepository;
    }

    protected function getMockTransactionDb()
    {
        $mockTransactionDb = Mockery::mock(stdClass::class, TransactionDbInterface::class);
        $mockTransactionDb->shouldReceive('commit');
        $mockTransactionDb->shouldReceive('rollback');

        return $mockTransactionDb;
    }

    protected function getMockCategoryRepository()
    {
        return Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
    }
}
