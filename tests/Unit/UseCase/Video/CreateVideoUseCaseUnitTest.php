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

    protected function createUseCase(
        int $timesCallMethodActionRepository = 1,
        int $timesCallMethodUpdateMediaRepository = 1,
        int $timesCallMethodCommitTransaction = 1,
        int $timesCallMethodRollbackTransaction = 0,
    )
    {
        $this->useCase = new CreateVideoUseCase(
            repository: $this->createMockRepository(
                timesCallAction: $timesCallMethodActionRepository,
                timesCallActionUpdateMedia: $timesCallMethodUpdateMediaRepository
            ),
            transaction: $this->createMockTransaction(
                timesCallCommit: $timesCallMethodCommitTransaction,
                timesCallRollback: $timesCallMethodRollbackTransaction
            ),
            storage: $this->createMockFileStorage(),
            eventManager: $this->createMockEventManager(),
            categoryRepository: $this->createMockCategoryRepository(),
            genreRepository: $this->createMockGenreRepository(),
            castMemberRepository: $this->createMockCastMemberRepository(),
        );
    }

    public function testExecuteInputOutput()
    {
        $this->createUseCase();

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

        $this->createUseCase(
            timesCallMethodActionRepository: 0,
            timesCallMethodUpdateMediaRepository: 0,
            timesCallMethodCommitTransaction: 0
        );

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

    /**
     * @dataProvider dataProviderFiles()
     */
    public function testUploadFiles(
        array $video,
        array $trailer,
        array $thumb,
        array $thumbHalf,
        array $banner,
    )
    {
        $this->createUseCase();

        $response = $this->useCase->execute(
            input: $this->createMockInputDto(
                videoFile: $video['value'],
                trailerFile: $trailer['value'],
                thumbFile: $thumb['value'],
                thumbHalf: $thumbHalf['value'],
                bannerFile: $banner['value'],
            )
        );

        $this->assertEquals($response->videoFile, $video['expected']);
        $this->assertEquals($response->trailerFile, $trailer['expected']);
        $this->assertEquals($response->thumbFile, $thumb['expected']);
        $this->assertEquals($response->thumbHalf, $thumbHalf['expected']);
        $this->assertEquals($response->bannerFile, $banner['expected']);
    }

    public static function dataProviderFiles(): array
    {
        return [
            [
                'video' => ['value' => ['tmp' => 'tmp/file.mp4'], 'expected' => 'path/file.mp4'],
                'trailer' => ['value' => ['tmp' => 'tmp/file.mp4'], 'expected' => 'path/file.mp4'],
                'thumb' => ['value' => ['tmp' => 'tmp/file.mp4'], 'expected' => 'path/file.mp4'],
                'thumbHalf' => ['value' => ['tmp' => 'tmp/file.mp4'], 'expected' => 'path/file.mp4'],
                'banner' => ['value' => ['tmp' => 'tmp/file.mp4'], 'expected' => 'path/file.mp4'],
            ],
            [
                'video' => ['value' => ['tmp' => 'tmp/file.mp4'], 'expected' => 'path/file.mp4'],
                'trailer' => ['value' => null, 'expected' => null],
                'thumb' => ['value' => ['tmp' => 'tmp/file.mp4'], 'expected' => 'path/file.mp4'],
                'thumbHalf' => ['value' => null, 'expected' => null],
                'banner' => ['value' => ['tmp' => 'tmp/file.mp4'], 'expected' => 'path/file.mp4'],
            ],
            [
                'video' => ['value' => null, 'expected' => null],
                'trailer' => ['value' => null, 'expected' => null],
                'thumb' => ['value' => null, 'expected' => null],
                'thumbHalf' => ['value' => null, 'expected' => null],
                'banner' => ['value' => null, 'expected' => null],
            ]
        ];
    }

    private function createMockRepository(
        int $timesCallAction,
        int $timesCallActionUpdateMedia,
    )
    {
        $mockRepository = Mockery::mock(stdClass::class, VideoRepositoryInterface::class);
        $mockRepository
            ->shouldReceive('insert')
            ->times($timesCallAction)
            ->andReturn($this->createMockEntity());
        $mockRepository
            ->shouldReceive('updateMedia')
            ->times($timesCallActionUpdateMedia);

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

    private function createMockTransaction(
        int $timesCallCommit,
        int $timesCallRollback,
    )
    {
        $mockRepository = Mockery::mock(stdClass::class, TransactionDbInterface::class);
        $mockRepository
            ->shouldReceive('commit')
            ->times($timesCallCommit);
        $mockRepository
            ->shouldReceive('rollback')
            ->times($timesCallRollback);

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
