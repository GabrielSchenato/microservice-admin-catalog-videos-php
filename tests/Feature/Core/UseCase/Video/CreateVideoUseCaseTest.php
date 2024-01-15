<?php

namespace Tests\Feature\Core\UseCase\Video;

use App\Models\CastMember;
use App\Models\Category;
use App\Models\Genre;
use Core\Domain\Enum\Rating;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\Repository\VideoRepositoryInterface;
use Core\UseCase\Interfaces\TransactionDbInterface;
use Core\UseCase\Video\Create\CreateVideoUseCase;
use Core\UseCase\Video\Create\DTO\VideoCreateInputDto;
use Core\UseCase\Video\Interfaces\VideoEventManagerInterface;
use Illuminate\Http\UploadedFile;
use Tests\Stubs\UploadFilesStub;
use Tests\TestCase;

class CreateVideoUseCaseTest extends TestCase
{

    /**
     * @dataProvider provider
     */
    public function testCreate(
        int  $categories,
        int  $genres,
        int  $castMembers,
        bool $withMediaVideo = false,
        bool $withTrailer = false,
        bool $withThumb = false,
        bool $withThumbHalf = false,
        bool $withBanner = false,
    ): void
    {
        $useCase = new CreateVideoUseCase(
            repository: $this->app->make(VideoRepositoryInterface::class),
            transaction: $this->app->make(TransactionDbInterface::class),
            storage: new UploadFilesStub(),
            eventManager: $this->app->make(VideoEventManagerInterface::class),
            categoryRepository: $this->app->make(CategoryRepositoryInterface::class),
            genreRepository: $this->app->make(GenreRepositoryInterface::class),
            castMemberRepository: $this->app->make(CastMemberRepositoryInterface::class),
        );

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

        $input = new VideoCreateInputDto(
            title: 'test',
            description: 'test',
            yearLaunched: 2023,
            duration: 50,
            opened: true,
            rating: Rating::RATE10,
            categoriesId: $categoriesId,
            genresId: $genresId,
            castMembersId: $castMembersId,
            videoFile: $withMediaVideo ? $file : null,
            trailerFile: $withTrailer ? $file : null,
            thumbFile: $withThumb ? $file : null,
            thumbHalf: $withThumbHalf ? $file : null,
            bannerFile: $withBanner ? $file : null,
        );

        $response = $useCase->execute($input);

        $this->assertEquals($input->title, $response->title);
        $this->assertEquals($input->description, $response->description);
        $this->assertEquals($input->yearLaunched, $response->yearLaunched);
        $this->assertEquals($input->duration, $response->duration);
        $this->assertEquals($input->opened, $response->opened);
        $this->assertEquals($input->rating, $response->rating);

        $this->assertCount($categories, $response->categoriesId);
        $this->assertEqualsCanonicalizing($input->categoriesId, $response->categoriesId);
        $this->assertCount($genres, $response->genresId);
        $this->assertEqualsCanonicalizing($input->genresId, $response->genresId);
        $this->assertCount($castMembers, $response->castMembersId);
        $this->assertEqualsCanonicalizing($input->castMembersId, $response->castMembersId);

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
                'castMembers' => 0
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
}
