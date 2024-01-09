<?php

namespace Tests\Feature\Core\UseCase\Category;

use App\Models\Category as Model;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use Core\UseCase\Category\UpdateCategoryUseCase;
use Core\UseCase\DTO\Category\UpdateCategory\CategoryUpdateInputDto;
use Tests\TestCase;

class UpdateCategoryUseCaseTest extends TestCase
{
    public function testUpdate(): void
    {
        $categoryDb = Model::factory()->create();
        $newName = 'New Name';

        $repository = new CategoryEloquentRepository(new Model());
        $useCase = new UpdateCategoryUseCase($repository);
        $response = $useCase->execute(
            new CategoryUpdateInputDto(
                id: $categoryDb->id,
                name: $newName
            )
        );

        $this->assertEquals($newName, $response->name);
        $this->assertEquals($categoryDb->description, $response->description);

        $this->assertDatabaseHas('categories', [
            'name' => $response->name
        ]);
    }
}
