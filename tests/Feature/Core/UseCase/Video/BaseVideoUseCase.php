<?php

namespace Tests\Feature\Core\UseCase\Video;

use App\Models\CastMember;
use App\Models\Category;
use App\Models\Genre;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\Repository\VideoRepositoryInterface;
use Core\UseCase\Interfaces\TransactionDbInterface;
use Exception;
use Illuminate\Database\Events\TransactionBeginning;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Tests\Stubs\UploadFilesStub;
use Tests\Stubs\VideoEventStub;
use Tests\TestCase;
use Throwable;

abstract class BaseVideoUseCase extends TestCase
{
    abstract public function getUseCase(): string;

    abstract public function inputDTO(
        array $categories = [],
        array $genres = [],
        array $castMembers = [],
        ?array $videoFile = null,
        ?array $trailerFile = null,
        ?array $bannerFile = null,
        ?array $thumbFile = null,
        ?array $thumbHalf = null,
    ): object;

    /**
     * @dataProvider provider
     */
    public function testAction(
        int $categories,
        int $genres,
        int $castMembers,
        bool $withMediaVideo = false,
        bool $withTrailer = false,
        bool $withThumb = false,
        bool $withThumbHalf = false,
        bool $withBanner = false,
    ): void {
        $sut = $this->makeSut();
        $categoriesId = Category::factory()->count($categories)->create()->pluck('id')->toArray();
        $genresId = Genre::factory()->count($genres)->create()->pluck('id')->toArray();
        $castMembersId = CastMember::factory()->count($castMembers)->create()->pluck('id')->toArray();

        $fakeFile = UploadedFile::fake()->create('video.mp4', 1, 'video/mp4');
        $file = [
            'tmp_name' => $fakeFile->getPathname(),
            'name' => $fakeFile->getClientOriginalName(),
            'type' => $fakeFile->getExtension(),
            'error' => $fakeFile->getError(),
        ];

        $input = $this->inputDTO(
            categories: $categoriesId,
            genres: $genresId,
            castMembers: $castMembersId,
            videoFile: $withMediaVideo ? $file : null,
            trailerFile: $withTrailer ? $file : null,
            bannerFile: $withBanner ? $file : null,
            thumbFile: $withThumb ? $file : null,
            thumbHalf: $withThumbHalf ? $file : null,
        );

        $response = $sut->execute($input);

        $this->assertEquals($input->title, $response->title);
        $this->assertEquals($input->description, $response->description);

        $this->assertCount($categories, $response->categories);
        $this->assertEqualsCanonicalizing($input->categoriesId, $response->categories);
        $this->assertCount($genres, $response->genres);
        $this->assertEqualsCanonicalizing($input->genresId, $response->genres);
        $this->assertCount($castMembers, $response->castMembers);
        $this->assertEqualsCanonicalizing($input->castMembersId, $response->castMembers);

        $this->assertTrue($withMediaVideo ? $response->videoFile !== null : $response->videoFile === null);
        $this->assertTrue($withTrailer ? $response->trailerFile !== null : $response->trailerFile === null);
        $this->assertTrue($withThumb ? $response->thumbFile !== null : $response->thumbFile === null);
        $this->assertTrue($withThumbHalf ? $response->thumbHalf !== null : $response->thumbHalf === null);
        $this->assertTrue($withBanner ? $response->bannerFile !== null : $response->bannerFile === null);
    }

    public static function provider(): array
    {
        return [
            'Test with all IDs and media video' => [
                'categories' => 3,
                'genres' => 3,
                'castMembers' => 3,
                'withMediaVideo' => true,
                'withTrailer' => false,
                'withThumb' => false,
                'withThumbHalf' => false,
                'withBanner' => false,
            ],
            'Test with categories and genres and without files' => [
                'categories' => 3,
                'genres' => 3,
                'castMembers' => 0,
            ],
            'Test with all IDs and all medias' => [
                'categories' => 2,
                'genres' => 2,
                'castMembers' => 2,
                'withMediaVideo' => true,
                'withTrailer' => true,
                'withThumb' => true,
                'withThumbHalf' => true,
                'withBanner' => true,
            ],
            'Test without IDs and all medias' => [
                'categories' => 0,
                'genres' => 0,
                'castMembers' => 0,
                'withMediaVideo' => true,
                'withTrailer' => true,
                'withThumb' => true,
                'withThumbHalf' => true,
                'withBanner' => true,
            ],
        ];
    }

    public function testTransactionException()
    {
        Event::listen(TransactionBeginning::class, fn () => throw new Exception('begin transaction'));
        try {
            $sut = $this->makeSut();
            $sut->execute($this->inputDTO());
            $this->fail();
        } catch (Throwable $th) {
            $this->assertDatabaseCount('videos', 0);
        }
    }

    public function testUploadFilesException()
    {
        Event::listen(UploadFilesStub::class, fn () => throw new Exception());

        try {
            $sut = $this->makeSut();
            $input = $this->inputDTO(videoFile: [
                'tmp_name' => 'video.mp4',
                'name' => 'video.mp4',
                'type' => 'tmp/video.mp4',
                'error' => 0,
            ]);
            $sut->execute($input);

            $this->fail();
        } catch (Throwable $th) {
            $this->assertDatabaseCount('videos', 0);
        }
    }

    public function testEventException()
    {
        Event::listen(VideoEventStub::class, fn () => throw new Exception());

        try {
            $sut = $this->makeSut();
            $input = $this->inputDTO(videoFile: [
                'tmp_name' => 'video.mp4',
                'name' => 'video.mp4',
                'type' => 'tmp/video.mp4',
                'error' => 0,
            ]);
            $sut->execute($input);

            $this->fail();
        } catch (Throwable $th) {
            $this->assertDatabaseCount('videos', 0);
        }
    }

    protected function makeSut()
    {
        return new ($this->getUseCase())(
            repository: $this->app->make(VideoRepositoryInterface::class),
            transaction: $this->app->make(TransactionDbInterface::class),
            storage: new UploadFilesStub(),
            eventManager: new VideoEventStub(),
            categoryRepository: $this->app->make(CategoryRepositoryInterface::class),
            genreRepository: $this->app->make(GenreRepositoryInterface::class),
            castMemberRepository: $this->app->make(CastMemberRepositoryInterface::class),
        );
    }
}
