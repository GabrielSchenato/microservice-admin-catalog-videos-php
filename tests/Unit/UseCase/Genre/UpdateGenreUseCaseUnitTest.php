<?php

namespace Tests\Unit\UseCase\Genre;

use Core\Domain\Entity\GenreEntity;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\ValueObject\Uuid;
use Core\UseCase\DTO\Genre\UpdateGenre\GenreUpdateInputDto;
use Core\UseCase\DTO\Genre\UpdateGenre\GenreUpdateOutputDto;
use Core\UseCase\Genre\UpdateGenreUseCase;
use Core\UseCase\Interfaces\TransactionDbInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid as RamseyUuid;
use stdClass;

class UpdateGenreUseCaseUnitTest extends TestCase
{
    public function testUpdateNewGenre()
    {
        $uuid = new Uuid(RamseyUuid::uuid4()->toString());
        $genreName = 'Old Genre';
        $mockEntity = $this->getMockEntity($genreName, $uuid);

        $mockRepository = $this->getMockRepository($mockEntity);

        $mockTransactionDb = $this->getMockTransactionDb();
        $mockCategoryRepository = $this->getMockCategoryRepository();
        $mockCategoryRepository->shouldReceive('getIdsListIds')->andReturn([
            $uuid->__toString()
        ]);

        $mockInputDto = Mockery::mock(GenreUpdateInputDto::class, [
            $genreName,
            $uuid,
            [$uuid->__toString()]
        ]);

        $useCase = new UpdateGenreUseCase($mockRepository, $mockTransactionDb, $mockCategoryRepository);
        $response = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(GenreUpdateOutputDto::class, $response);
    }

    public function testUpdateNewGenreCategoryNotFound()
    {
        $this->expectException(NotFoundException::class);

        $uuid = new Uuid(RamseyUuid::uuid4()->toString());
        $genreName = 'New Genre';
        $mockEntity = $this->getMockEntity($genreName, $uuid);

        $mockRepository = $this->getMockRepository($mockEntity, 0);

        $mockTransactionDb = $this->getMockTransactionDb();
        $mockCategoryRepository = $this->getMockCategoryRepository();
        $mockCategoryRepository->shouldReceive('getIdsListIds');

        $mockInputDto = Mockery::mock(GenreUpdateInputDto::class, [
            $genreName,
            $uuid,
            [$uuid->__toString()]
        ]);

        $useCase = new UpdateGenreUseCase($mockRepository, $mockTransactionDb, $mockCategoryRepository);
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
            $uuid
        ]);
        $mockEntity->shouldReceive('id')->andReturn($uuid);
        $mockEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));
        $mockEntity->shouldReceive('update');
        $mockEntity->shouldReceive('addCategory');
        return $mockEntity;
    }


    protected function getMockRepository($mockEntity, int $times = 1)
    {
        $mockRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockRepository
            ->shouldReceive('update')
            ->times($times)
            ->andReturn($mockEntity);
        $mockRepository
            ->shouldReceive('findById')
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
