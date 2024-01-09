<?php

namespace Tests\Feature\App\Repositories\Eloquent;

use App\Models\Category as Model;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use Core\Domain\Entity\CategoryEntity;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Tests\TestCase;

class CategoryEloquentRepositoryTest extends TestCase
{

    public function testInsert(): void
    {
        $repository = new CategoryEloquentRepository(new Model());
        $entity = new CategoryEntity(
            name: 'Teste'
        );

        $response = $repository->insert($entity);

        $this->assertInstanceOf(CategoryRepositoryInterface::class, $repository);
        $this->assertInstanceOf(CategoryEntity::class, $response);
        $this->assertDatabaseHas('categories', [
            'name' => $entity->name
        ]);
    }
}
