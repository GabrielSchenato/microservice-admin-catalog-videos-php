<?php

namespace Core\UseCase\Genre;

use App\Models\Category as ModelCategory;
use App\Models\Genre as Model;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use App\Repositories\Eloquent\GenreEloquentRepository;
use App\Repositories\Transaction\TransactionDb;
use Core\UseCase\DTO\Genre\CreateGenre\GenreCreateInputDto;
use Tests\TestCase;

class CreateGenreUseCaseTest extends TestCase
{

    public function testCreate(): void
    {
        $name = 'Teste';
        $repository = new GenreEloquentRepository(new Model());
        $categoryRepository = new CategoryEloquentRepository(new ModelCategory());
        $useCase = new CreateGenreUseCase(
            $repository,
            new TransactionDb(),
            $categoryRepository
        );
        $response = $useCase->execute(
            new GenreCreateInputDto(
                name: $name
            )
        );

        $this->assertEquals($name, $response->name);
        $this->assertNotEmpty($response->id);
        $this->assertDatabaseHas('genres', [
            'id' => $response->id
        ]);
    }
}
