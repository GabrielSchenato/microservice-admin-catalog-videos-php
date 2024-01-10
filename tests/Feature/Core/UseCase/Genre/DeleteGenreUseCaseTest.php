<?php

namespace Core\UseCase\Genre;

use App\Models\Genre as Model;
use App\Repositories\Eloquent\GenreEloquentRepository;
use Core\UseCase\DTO\Genre\GenreInputDto;
use Tests\TestCase;

class DeleteGenreUseCaseTest extends TestCase
{
    public function testDelete(): void
    {
        $categoryDb = Model::factory()->create();

        $repository = new GenreEloquentRepository(new Model());
        $useCase = new DeleteGenreUseCase($repository);
        $useCase->execute(
            new GenreInputDto(
                id: $categoryDb->id
            )
        );

        $this->assertSoftDeleted('genres', [
            'id' => $categoryDb->id
        ]);
    }
}
