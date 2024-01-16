<?php

namespace Tests\Feature\Core\UseCase\Category;

use App\Models\Category as Model;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use Core\UseCase\Category\CreateCategoryUseCase;
use Core\UseCase\DTO\Category\CreateCategory\CategoryCreateInputDto;
use Tests\TestCase;

class CreateCategoryUseCaseTest extends TestCase
{
    public function testCreate(): void
    {
        $name = 'Teste';
        $repository = new CategoryEloquentRepository(new Model());
        $useCase = new CreateCategoryUseCase($repository);
        $response = $useCase->execute(
            new CategoryCreateInputDto(
                name: $name
            )
        );

        $this->assertEquals($name, $response->name);
        $this->assertNotEmpty($response->id);
        $this->assertDatabaseHas('categories', [
            'id' => $response->id,
        ]);
    }
}
