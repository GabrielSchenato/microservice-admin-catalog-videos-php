<?php

namespace Core\UseCase\Genre;

use App\Models\Genre as Model;
use App\Repositories\Eloquent\GenreEloquentRepository;
use Core\UseCase\Genre\ListGenreUseCase;
use Core\UseCase\DTO\Genre\GenreInputDto;
use Tests\TestCase;

class ListGenreUseCaseTest extends TestCase
{
    public function testList(): void
    {
        $categoryDb = Model::factory()->create();

        $repository = new GenreEloquentRepository(new Model());
        $useCase = new ListGenreUseCase($repository);
        $response = $useCase->execute(
            new GenreInputDto(
                id: $categoryDb->id
            )
        );

        $this->assertEquals($categoryDb->id, $response->id);
        $this->assertEquals($categoryDb->name, $response->name);
    }
}
