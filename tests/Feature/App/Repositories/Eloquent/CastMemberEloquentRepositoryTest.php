<?php

namespace Tests\Feature\App\Repositories\Eloquent;

use App\Models\CastMember as Model;
use App\Repositories\Eloquent\CastMemberEloquentRepository;
use Core\Domain\Entity\CastMemberEntity;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;
use Core\Domain\ValueObject\Uuid;
use Tests\TestCase;

class CastMemberEloquentRepositoryTest extends TestCase
{
    protected CastMemberRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new CastMemberEloquentRepository(new Model());
    }

    public function testInsert(): void
    {
        $entity = new CastMemberEntity(
            name: 'Teste',
            type: CastMemberType::DIRECTOR
        );

        $response = $this->repository->insert($entity);

        $this->assertInstanceOf(CastMemberRepositoryInterface::class, $this->repository);
        $this->assertInstanceOf(CastMemberEntity::class, $response);
        $this->assertDatabaseHas('cast_members', [
            'name' => $entity->name,
        ]);
    }

    public function testFindById(): void
    {
        $castMember = Model::factory()->create();
        $response = $this->repository->findById($castMember->id);

        $this->assertInstanceOf(CastMemberEntity::class, $response);
        $this->assertEquals($castMember->id, $response->id());
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
        $cast_members = Model::factory()->count(10)->create();
        $response = $this->repository->findAll();

        $this->assertCount(count($cast_members), $response);
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
        $castMemberDb = Model::factory()->create();
        $castMember = new CastMemberEntity(
            name: $name,
            type: CastMemberType::DIRECTOR,
            id: new Uuid($castMemberDb->id)
        );
        $response = $this->repository->update($castMember);

        $this->assertInstanceOf(CastMemberEntity::class, $response);
        $this->assertNotEquals($castMember->name, $castMemberDb->name);
        $this->assertEquals($name, $response->name);
    }

    public function testUpdateIdNotFound(): void
    {
        try {
            $castMember = new CastMemberEntity(name: 'Teste', type: CastMemberType::DIRECTOR);
            $this->repository->update($castMember);
            $this->fail();
        } catch (\Throwable $th) {
            $this->assertInstanceOf(NotFoundException::class, $th);
        }
    }

    public function testDelete(): void
    {
        $castMemberDb = Model::factory()->create();
        $response = $this->repository->delete($castMemberDb->id);

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
