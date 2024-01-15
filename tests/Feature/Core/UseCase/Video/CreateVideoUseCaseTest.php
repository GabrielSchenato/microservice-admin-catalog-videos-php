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
use Core\UseCase\Interfaces\FileStorageInterface;
use Core\UseCase\Interfaces\TransactionDbInterface;
use Core\UseCase\Video\Create\CreateVideoUseCase;
use Core\UseCase\Video\Create\DTO\VideoCreateInputDto;
use Core\UseCase\Video\Interfaces\VideoEventManagerInterface;
use Tests\TestCase;

class CreateVideoUseCaseTest extends TestCase
{

    public function testCreate(): void
    {
        $useCase = new CreateVideoUseCase(
            repository: $this->app->make(VideoRepositoryInterface::class),
            transaction: $this->app->make(TransactionDbInterface::class),
            storage: $this->app->make(FileStorageInterface::class),
            eventManager: $this->app->make(VideoEventManagerInterface::class),
            categoryRepository: $this->app->make(CategoryRepositoryInterface::class),
            genreRepository: $this->app->make(GenreRepositoryInterface::class),
            castMemberRepository: $this->app->make(CastMemberRepositoryInterface::class),
        );

        $categoriesId = Category::factory()->count(3)->create()->pluck('id')->toArray();
        $genresId = Genre::factory()->count(3)->create()->pluck('id')->toArray();
        $castMembersId = CastMember::factory()->count(3)->create()->pluck('id')->toArray();

        $input = new VideoCreateInputDto(
            title: 'test',
            description: 'test',
            yearLaunched: 2023,
            duration: 50,
            opened: true,
            rating: Rating::RATE10,
            categoriesId: $categoriesId,
            genresId: $genresId,
            castMembersId: $castMembersId
        );

        $response = $useCase->execute($input);

        $this->assertEquals($input->title, $response->title);
        $this->assertEquals($input->description, $response->description);
        $this->assertEquals($input->yearLaunched, $response->yearLaunched);
        $this->assertEquals($input->duration, $response->duration);
        $this->assertEquals($input->opened, $response->opened);
        $this->assertEquals($input->rating, $response->rating);

        $this->assertCount(count($input->categoriesId), $response->categoriesId);
        $this->assertCount(count($input->genresId), $response->genresId);
        $this->assertCount(count($input->castMembersId), $response->castMembersId);
    }
}
