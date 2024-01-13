<?php

namespace Tests\Unit\UseCase\Video;

use Core\Domain\Entity\VideoEntity;
use Core\Domain\Enum\Rating;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\Repository\VideoRepositoryInterface;
use Core\UseCase\Interfaces\FileStorageInterface;
use Core\UseCase\Interfaces\TransactionDbInterface;
use Core\UseCase\Video\Create\CreateVideoUseCase;
use Core\UseCase\Video\Create\DTO\VideoCreateInputDto;
use Core\UseCase\Video\Create\DTO\VideoCreateOutputDto;
use Core\UseCase\Video\Interfaces\VideoEventManagerInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;

class CreateVideoUseCaseUnitTest extends TestCase
{
    protected CreateVideoUseCase $useCase;

    protected function setUp(): void
    {
        $this->useCase = new CreateVideoUseCase(
            repository: $this->createMockRepository(),
            transaction: $this->createMockTransaction(),
            storage: $this->createMockFileStorage(),
            eventManager: $this->createMockEventManager(),
            categoryRepository: $this->createMockCategoryRepository(),
            genreRepository: $this->createMockGenreRepository(),
            castMemberRepository: $this->createMockCastMemberRepository(),
        );

        parent::setUp();
    }

    public function testExecuteInputOutput()
    {
        $response = $this->useCase->execute(
            input: $this->createMockInputDto()
        );

        $this->assertInstanceOf(VideoCreateOutputDto::class, $response);
    }

    public function testExceptionCategoriesIds()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Category uuid-1 not found');

        $this->useCase->execute(
            input: $this->createMockInputDto(categoriesId: [
                'uuid-1'
            ])
        );
    }

    public function testExceptionMessageCategoriesIds()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Categories uuid-1, uuid-2 not found');

        $this->useCase->execute(
            input: $this->createMockInputDto(categoriesId: [
                'uuid-1',
                'uuid-2'
            ])
        );
    }

    private function createMockRepository()
    {
        $mockRepository = Mockery::mock(stdClass::class, VideoRepositoryInterface::class);
        $mockRepository
            ->shouldReceive('insert')
            ->andReturn($this->createMockEntity());
        $mockRepository
            ->shouldReceive('updateMedia');

        return $mockRepository;
    }

    private function createMockCategoryRepository(array $categoriesResponse = [])
    {
        $mockRepository = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $mockRepository
            ->shouldReceive('getIdsListIds')
            ->andReturn($categoriesResponse);

        return $mockRepository;
    }

    private function createMockGenreRepository(array $genresResponse = [])
    {
        $mockRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockRepository
            ->shouldReceive('getIdsListIds')
            ->andReturn($genresResponse);

        return $mockRepository;
    }

    private function createMockCastMemberRepository(array $castMembersResponse = [])
    {
        $mockRepository = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $mockRepository
            ->shouldReceive('getIdsListIds')
            ->andReturn($castMembersResponse);

        return $mockRepository;
    }

    private function createMockTransaction()
    {
        $mockRepository = Mockery::mock(stdClass::class, TransactionDbInterface::class);
        $mockRepository->shouldReceive('commit');
        $mockRepository->shouldReceive('rollback');

        return $mockRepository;
    }

    private function createMockFileStorage()
    {
        $mockRepository = Mockery::mock(stdClass::class, FileStorageInterface::class);
        $mockRepository->shouldReceive('store')->andReturn('path/file.png');

        return $mockRepository;
    }

    private function createMockEventManager()
    {
        $mockRepository = Mockery::mock(stdClass::class, VideoEventManagerInterface::class);
        $mockRepository->shouldReceive('dispatch');

        return $mockRepository;
    }

    private function createMockInputDto(
        array $categoriesId = [],
        array $genresId = [],
        array $castMembersId = []
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
        ]);
    }

    private function createMockEntity()
    {
        return Mockery::mock(VideoEntity::class, [
            'title',
            'desc',
            2023,
            12,
            true,
            Rating::RATE12,
        ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
