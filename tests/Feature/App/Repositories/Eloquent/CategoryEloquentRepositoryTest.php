<?php

namespace Tests\Feature\App\Repositories\Eloquent;

use App\Models\Category as Model;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use Core\Domain\Entity\CategoryEntity;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;
use Tests\TestCase;

class CategoryEloquentRepositoryTest extends TestCase
{
    protected CategoryRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new CategoryEloquentRepository(new Model());
    }

    public function testInsert(): void
    {
        $entity = new CategoryEntity(
            name: 'Teste'
        );

        $response = $this->repository->insert($entity);

        $this->assertInstanceOf(CategoryRepositoryInterface::class, $this->repository);
        $this->assertInstanceOf(CategoryEntity::class, $response);
        $this->assertDatabaseHas('categories', [
            'name' => $entity->name,
        ]);
    }

    public function testFindById(): void
    {
        $category = Model::factory()->create();
        $response = $this->repository->findById($category->id);

        $this->assertInstanceOf(CategoryEntity::class, $response);
        $this->assertEquals($category->id, $response->id());
    }

    public function testFindByIdNotFound(): void
    {
        try {
            $this->repository->findById('fakeValue');
            $this->fail();
        } catch (\Throwable $th) {
            $this->assertInstanceOf(NotFoundException::class, $th);
        }
    }

    public function testFindAll(): void
    {
        $categories = Model::factory()->count(10)->create();
        $response = $this->repository->findAll();

        $this->assertCount(count($categories), $response);
    }

    public function testPaginate(): void
    {
        Model::factory()->count(20)->create();
        $response = $this->repository->paginate();

        $this->assertInstanceOf(PaginationInterface::class, $response);
        $this->assertCount(15, $response->items());
    }

    public function testPaginateEmpty(): void
    {
        $response = $this->repository->paginate();

        $this->assertInstanceOf(PaginationInterface::class, $response);
        $this->assertCount(0, $response->items());
    }

    public function testUpdate(): void
    {
        $name = 'updated name';
        $categoryDb = Model::factory()->create();
        $category = new CategoryEntity(
            id: $categoryDb->id,
            name: $name
        );
        $response = $this->repository->update($category);

        $this->assertInstanceOf(CategoryEntity::class, $response);
        $this->assertNotEquals($category->name, $categoryDb->name);
        $this->assertEquals($name, $response->name);
    }

    public function testUpdateIdNotFound(): void
    {
        try {
            $category = new CategoryEntity(name: 'Teste');
            $this->repository->update($category);
            $this->fail();
        } catch (\Throwable $th) {
            $this->assertInstanceOf(NotFoundException::class, $th);
        }
    }

    public function testDelete(): void
    {
        $categoryDb = Model::factory()->create();
        $response = $this->repository->delete($categoryDb->id);

        $this->assertTrue($response);
    }

    public function testDeleteIdNotFound(): void
    {
        try {
            $this->repository->delete('fakeValue');
            $this->fail();
        } catch (\Throwable $th) {
            $this->assertInstanceOf(NotFoundException::class, $th);
        }
    }
}
