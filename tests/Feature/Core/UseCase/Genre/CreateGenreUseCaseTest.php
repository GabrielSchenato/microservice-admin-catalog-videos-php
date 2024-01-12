<?php

namespace Tests\Feature\Core\UseCase\Genre;

use App\Models\Category as ModelCategory;
use App\Models\Genre as Model;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use App\Repositories\Eloquent\GenreEloquentRepository;
use App\Repositories\Transaction\TransactionDb;
use Core\Domain\Exception\NotFoundException;
use Core\UseCase\DTO\Genre\CreateGenre\GenreCreateInputDto;
use Tests\TestCase;
use Throwable;

class CreateGenreUseCaseTest extends TestCase
{

    public function testCreate(): void
    {
        $name = 'Teste';
        $useCase = $this->getUseCase();

        $categories = ModelCategory::factory()->count(10)->create();
        $categoriesIds = $categories->pluck('id')->toArray();

        $response = $useCase->execute(
            new GenreCreateInputDto(
                name: $name,
                categoriesId: $categoriesIds
            )
        );

        $this->assertEquals($name, $response->name);
        $this->assertNotEmpty($response->id);
        $this->assertDatabaseHas('genres', [
            'id' => $response->id
        ]);
        $this->assertDatabaseCount('category_genre', 10);
    }

    public function testCreateWithCategoriesIdsInvalid(): void
    {
        $this->expectException(NotFoundException::class);

        $name = 'Teste';
        $useCase = $this->getUseCase();

        $categories = ModelCategory::factory()->count(10)->create();
        $categoriesIds = $categories->pluck('id')->toArray();
        $categoriesIds[] = 'fake_id';

        $useCase->execute(
            new GenreCreateInputDto(
                name: $name,
                categoriesId: $categoriesIds
            )
        );
    }

    public function testTransactionCreate(): void
    {
        $name = 'Teste';
        $useCase = $this->getUseCase();

        $categories = ModelCategory::factory()->count(10)->create();
        $categoriesIds = $categories->pluck('id')->toArray();
        $categoriesIds[] = 'fake_id';

        try {
            $useCase->execute(
                new GenreCreateInputDto(
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
     * @return CreateGenreUseCase
     */
    protected function getUseCase(): CreateGenreUseCase
    {
        $repository = new GenreEloquentRepository(new Model());
        $categoryRepository = new CategoryEloquentRepository(new ModelCategory());
        return new CreateGenreUseCase(
            $repository,
            new TransactionDb(),
            $categoryRepository
        );
    }
}
