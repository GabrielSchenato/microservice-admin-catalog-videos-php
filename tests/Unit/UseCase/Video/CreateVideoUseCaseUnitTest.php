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

    /**
     * @dataProvider dataProviderIds
     */
    public function testExceptionCategoriesIds(
        string $label,
        array  $ids
    )
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(sprintf(
            '%s %s not found',
            $label,
            implode(', ', $ids
            )
        ));

        $this->useCase->execute(
            input: $this->createMockInputDto(categoriesId: $ids)
        );
    }

    public static function dataProviderIds(): array
    {
        return [
            ['Category', ['uud-1']],
            ['Categories', ['uud-1', 'uud-2']],
        ];
    }

    public function testUploadFiles()
    {
        $response = $this->useCase->execute(
            input: $this->createMockInputDto(
                videoFile: ['tmp' => 'tmp/file.mp4'],
                trailerFile: ['tmp' => 'tmp/file.mp4'],
                thumbFile: ['tmp' => 'tmp/file.png'],
                thumbHalf: ['tmp' => 'tmp/file.png']
            )
        );

        $this->assertNotNull($response->videoFile);
        $this->assertNotNull($response->trailerFile);
        $this->assertNotNull($response->thumbFile);
        $this->assertNotNull($response->thumbHalf);
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
        $mockRepository->shouldReceive('store')->andReturn('path/file.mp4');

        return $mockRepository;
    }

    private function createMockEventManager()
    {
        $mockRepository = Mockery::mock(stdClass::class, VideoEventManagerInterface::class);
        $mockRepository->shouldReceive('dispatch');

        return $mockRepository;
    }

    private function createMockInputDto(
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
