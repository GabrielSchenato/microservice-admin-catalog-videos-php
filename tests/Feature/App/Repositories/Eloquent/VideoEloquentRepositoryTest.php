<?php

namespace App\Repositories\Eloquent;

use App\Models\CastMember;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Video as Model;
use Core\Domain\Entity\VideoEntity;
use Core\Domain\Enum\MediaStatus;
use Core\Domain\Enum\Rating;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\PaginationInterface;
use Core\Domain\Repository\VideoRepositoryInterface;
use Core\Domain\ValueObject\Media;
use Core\Domain\ValueObject\Uuid;
use DateTime;
use Tests\TestCase;

class VideoEloquentRepositoryTest extends TestCase
{
    protected VideoRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new VideoEloquentRepository(new Model());
    }

    public function testImplementsInterface(): void
    {
        $this->assertInstanceOf(VideoRepositoryInterface::class, $this->repository);
    }

    public function testInsert(): void
    {
        $entity = new VideoEntity(
            title: 'title',
            description: 'desc',
            yearLaunched: 2023,
            duration: 12,
            opened: true,
            rating: Rating::RATE12
        );

        $response = $this->repository->insert($entity);

        $this->assertInstanceOf(VideoEntity::class, $response);
        $this->assertDatabaseHas('videos', [
            'title' => $entity->title
        ]);
    }

    public function testInsertWithRelationships(): void
    {
        $categories = Category::factory()->count(4)->create();
        $genres = Genre::factory()->count(4)->create();
        $castMembers = CastMember::factory()->count(4)->create();

        $entity = new VideoEntity(
            title: 'title',
            description: 'desc',
            yearLaunched: 2023,
            duration: 12,
            opened: true,
            rating: Rating::RATE12
        );

        foreach ($categories as $category) {
            $entity->addCategory($category->id);
        }

        foreach ($genres as $genre) {
            $entity->addGenre($genre->id);
        }

        foreach ($castMembers as $castMember) {
            $entity->addCastMember($castMember->id);
        }

        $response = $this->repository->insert($entity);

        $this->assertInstanceOf(VideoEntity::class, $response);
        $this->assertDatabaseHas('videos', [
            'title' => $entity->title
        ]);
        $this->assertDatabaseCount('category_video', 4);
        $this->assertDatabaseCount('genre_video', 4);
        $this->assertDatabaseCount('cast_member_video', 4);

        $this->assertEquals($categories->pluck('id')->toArray(), $response->categoriesId);
        $this->assertEquals($genres->pluck('id')->toArray(), $response->genresId);
        $this->assertEquals($castMembers->pluck('id')->toArray(), $response->castMembersId);
    }

    public function testFindByIdNotFound(): void
    {
        $this->expectException(NotFoundException::class);
        $this->repository->findById('fakeValue');
    }

    public function testFindById(): void
    {
        $video = Model::factory()->create();
        $response = $this->repository->findById($video->id);

        $this->assertInstanceOf(VideoEntity::class, $response);
        $this->assertEquals($video->id, $response->id());
    }

    public function testFindAll(): void
    {
        $videos = Model::factory()->count(10)->create();
        $response = $this->repository->findAll();

        $this->assertCount(count($videos), $response);
    }

    public function testFindAllWithFilter(): void
    {
        $title = 'Test';
        Model::factory()->count(10)->create();
        Model::factory()->count(10)->create([
            'title' => $title
        ]);
        $response = $this->repository->findAll(
            filter: $title
        );

        $this->assertCount(10, $response);
        $this->assertDatabaseCount('videos', 20);
    }

    /**
     * @dataProvider dataProviderPagination
     */
    public function testPagination(
        int $page,
        int $totalPage,
        int $total = 50,
    )
    {
        Model::factory()->count($total)->create();

        $response = $this->repository->paginate(
            page: $page,
            totalPage: $totalPage
        );

        $this->assertCount($totalPage, $response->items());
        $this->assertEquals($total, $response->total());
        $this->assertEquals($page, $response->currentPage());
        $this->assertEquals($totalPage, $response->perPage());
    }

    public static function dataProviderPagination(): array
    {
        return [
            [
                'page' => 1,
                'totalPage' => 10,
                'total' => 100,
            ], [
                'page' => 2,
                'totalPage' => 15,
            ], [
                'page' => 3,
                'totalPage' => 15,
            ],
        ];
    }

    public function testPaginateEmpty(): void
    {
        $response = $this->repository->paginate();

        $this->assertInstanceOf(PaginationInterface::class, $response);
        $this->assertCount(0, $response->items());
    }

    public function testUpdateIdNotFound(): void
    {
        $this->expectException(NotFoundException::class);
        $this->repository->update(new VideoEntity(
            title: 'title',
            description: 'desc',
            yearLaunched: 2023,
            duration: 12,
            opened: true,
            rating: Rating::RATE12
        ));
    }

    public function testUpdate(): void
    {
        $categories = Category::factory()->count(10)->create();
        $genres = Genre::factory()->count(10)->create();
        $castMembers = CastMember::factory()->count(10)->create();

        $videoDb = Model::factory()->create();

        $this->assertDatabaseHas('videos', [
            'title' => $videoDb->title,
        ]);

        $entity = new VideoEntity(
            title: 'Test',
            description: 'Test',
            yearLaunched: 2026,
            duration: 1,
            opened: true,
            rating: Rating::L,
            id: new Uuid($videoDb->id),
            createdAt: new DateTime($videoDb->created_at),
        );

        foreach ($categories as $category) {
            $entity->addCategory($category->id);
        }
        foreach ($genres as $genre) {
            $entity->addGenre($genre->id);
        }
        foreach ($castMembers as $castMember) {
            $entity->addCastMember($castMember->id);
        }

        $entityInDb = $this->repository->update($entity);

        $this->assertDatabaseHas('videos', [
            'title' => 'Test',
        ]);

        $this->assertDatabaseCount('category_video', 10);
        $this->assertDatabaseCount('genre_video', 10);
        $this->assertDatabaseCount('cast_member_video', 10);

        $this->assertEquals($categories->pluck('id')->toArray(), $entityInDb->categoriesId);
        $this->assertEquals($genres->pluck('id')->toArray(), $entityInDb->genresId);
        $this->assertEquals($castMembers->pluck('id')->toArray(), $entityInDb->castMembersId);
    }

    public function testDeleteIdNotFound(): void
    {
        $this->expectException(NotFoundException::class);
        $this->repository->delete('fakeValue');
    }

    public function testDelete(): void
    {
        $videoDb = Model::factory()->create();
        $response = $this->repository->delete($videoDb->id);

        $this->assertTrue($response);
        $this->assertSoftDeleted('videos', [
            'id' => $videoDb->id,
        ]);
    }

    public function testInsertWithMediaTrailer()
    {
        $entity = new VideoEntity(
            title: 'Test',
            description: 'Test',
            yearLaunched: 2026,
            duration: 1,
            opened: true,
            rating: Rating::L,
            trailerFile: new Media(
                filePath: 'test.mp4',
                mediaStatus: MediaStatus::PROCESSING,
            ),
        );
        $this->repository->insert($entity);

        $this->assertDatabaseCount('media_videos', 0);
        $this->repository->updateMedia($entity);
        $this->assertDatabaseHas('media_videos', [
            'video_id' => $entity->id(),
            'file_path' => 'test.mp4',
            'media_status' => MediaStatus::PROCESSING->value,
        ]);

        $entity->setTrailerFile(new Media(
            filePath: 'test2.mp4',
            mediaStatus: MediaStatus::COMPLETE,
            encodedPath: 'test2.xpto',
        ));

        $entityDb = $this->repository->updateMedia($entity);
        $this->assertDatabaseCount('media_videos', 1);
        $this->assertDatabaseHas('media_videos', [
            'video_id' => $entity->id(),
            'file_path' => 'test2.mp4',
            'media_status' => MediaStatus::COMPLETE->value,
            'encoded_path' => 'test2.xpto',
        ]);

        $this->assertNotNull($entityDb->getTrailerFile());
    }
}
