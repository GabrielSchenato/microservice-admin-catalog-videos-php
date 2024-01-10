<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Models\Genre as Model;
use Core\Domain\Entity\GenreEntity;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;
use Core\Domain\ValueObject\Uuid;
use Tests\TestCase;

class GenreEloquentRepositoryTest extends TestCase
{
    protected GenreRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new GenreEloquentRepository(new Model());
    }

    public function testInsert(): void
    {
        $entity = new GenreEntity(
            name: 'Teste'
        );

        $response = $this->repository->insert($entity);

        $this->assertInstanceOf(GenreRepositoryInterface::class, $this->repository);
        $this->assertInstanceOf(GenreEntity::class, $response);
        $this->assertDatabaseHas('genres', [
            'name' => $entity->name
        ]);
    }

    public function testFindById(): void
    {
        $genre = Model::factory()->create();
        $response = $this->repository->findById($genre->id);

        $this->assertInstanceOf(GenreEntity::class, $response);
        $this->assertEquals($genre->id, $response->id());
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
        $genres = Model::factory()->count(10)->create();
        $response = $this->repository->findAll();

        $this->assertCount(count($genres), $response);
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
        $genreDb = Model::factory()->create();
        $genre = new GenreEntity(
            name: $name,
            id: new Uuid($genreDb->id)
        );
        $response = $this->repository->update($genre);

        $this->assertInstanceOf(GenreEntity::class, $response);
        $this->assertNotEquals($genre->name, $genreDb->name);
        $this->assertEquals($name, $response->name);
    }

    public function testUpdateIdNotFound(): void
    {
        try {
            $genre = new GenreEntity(name: 'Teste');
            $this->repository->update($genre);
            $this->fail();
        } catch (\Throwable $th) {
            $this->assertInstanceOf(NotFoundException::class, $th);
        }
    }

    public function testDelete(): void
    {
        $genreDb = Model::factory()->create();
        $response = $this->repository->delete($genreDb->id);

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

    public function testeInsertWithRelationships(): void
    {
        $categories = Category::factory()->count(4)->create();

        $genre = new GenreEntity(name: 'Teste');
        foreach ($categories as $category) {
            $genre->addCategory($category->id);
        }

        $response = $this->repository->insert($genre);
        $this->assertDatabaseHas('genres', [
            'id' => $response->id
        ]);
        $this->assertDatabaseCount('category_genre', 4);
    }
}
