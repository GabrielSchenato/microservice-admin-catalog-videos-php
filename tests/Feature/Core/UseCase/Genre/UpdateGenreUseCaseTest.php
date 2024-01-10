<?php

namespace Core\UseCase\Genre;

use App\Models\Category as ModelCategory;
use App\Models\Genre as Model;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use App\Repositories\Eloquent\GenreEloquentRepository;
use App\Repositories\Transaction\TransactionDb;
use Core\Domain\Exception\NotFoundException;
use Core\UseCase\DTO\Genre\UpdateGenre\GenreUpdateInputDto;
use Tests\TestCase;
use Throwable;

class UpdateGenreUseCaseTest extends TestCase
{
    public function testUpdate(): void
    {
        $categoryDb = Model::factory()->create();
        $newName = 'New Name';

        $useCase = $this->getUseCase();
        $categories = ModelCategory::factory()->count(10)->create();
        $categoriesIds = $categories->pluck('id')->toArray();

        $response = $useCase->execute(
            new GenreUpdateInputDto(
                id: $categoryDb->id,
                name: $newName,
                categoriesId: $categoriesIds
            )
        );

        $this->assertEquals($newName, $response->name);

        $this->assertDatabaseHas('genres', [
            'name' => $response->name
        ]);
        $this->assertDatabaseCount('category_genre', 10);
    }

    public function testUpdateWithCategoriesIdsInvalid(): void
    {
        $this->expectException(NotFoundException::class);

        $categoryDb = Model::factory()->create();
        $newName = 'New Name';

        $useCase = $this->getUseCase();
        $categories = ModelCategory::factory()->count(10)->create();
        $categoriesIds = $categories->pluck('id')->toArray();
        $categoriesIds[] = 'fake_id';

        $useCase->execute(
            new GenreUpdateInputDto(
                id: $categoryDb->id,
                name: $newName,
                categoriesId: $categoriesIds
            )
        );
    }

    public function testTransactionCreate(): void
    {
        $name = 'Teste';
        $useCase = $this->getUseCase();

        $categoryDb = Model::factory()->create();
        $categories = ModelCategory::factory()->count(10)->create();
        $categoriesIds = $categories->pluck('id')->toArray();
        $categoriesIds[] = 'fake_id';

        try {
            $useCase->execute(
                new GenreUpdateInputDto(
                    id: $categoryDb->id,
                    name: $name,
                    categoriesId: $categoriesIds
                )
            );
        } catch (Throwable $th) {
            $this->assertDatabaseCount('genres', 0);
            $this->assertDatabaseCount('category_genre', 0);
        }
    }

    /**
     * @return UpdateGenreUseCase
     */
    protected function getUseCase(): UpdateGenreUseCase
    {
        $repository = new GenreEloquentRepository(new Model());
        $categoryRepository = new CategoryEloquentRepository(new ModelCategory());
        return new UpdateGenreUseCase(
            $repository,
            new TransactionDb(),
            $categoryRepository
        );
    }
}
