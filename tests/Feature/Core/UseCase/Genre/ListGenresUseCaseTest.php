<?php

namespace Tests\Feature\Core\UseCase\Genre;

use App\Models\Genre as Model;
use App\Repositories\Eloquent\GenreEloquentRepository;
use Core\Domain\Repository\PaginationInterface;
use Core\UseCase\Genre\ListGenresUseCase;
use Core\UseCase\DTO\Genre\ListGenres\ListGenresInputDto;
use Core\UseCase\DTO\Genre\ListGenres\ListGenresOutputDto;
use Tests\TestCase;

class ListGenresUseCaseTest extends TestCase
{
    public function testListAllEmpty(): void
    {
        $response = $this->createUseCase();

        $this->assertCount(0, $response->items());
    }

    public function testListAll(): void
    {
        $categoriesDb = Model::factory()->count(20)->create();
        $response = $this->createUseCase();

        $this->assertCount(15, $response->items());
        $this->assertEquals(count($categoriesDb), $response->total());
    }

    private function createUseCase(): PaginationInterface
    {
        $repository = new GenreEloquentRepository(new Model());
        $useCase = new ListGenresUseCase($repository);
        return $useCase->execute(new ListGenresInputDto());
    }
}
