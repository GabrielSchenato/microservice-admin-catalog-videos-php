<?php

namespace Tests\Unit\UseCase\CastMember;

use Core\Domain\Enum\CastMemberType;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;
use Core\UseCase\CastMember\ListCastMembersUseCase;
use Core\UseCase\DTO\CastMember\ListCastMembers\ListCastMembersInputDto;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;

class ListCastMembersUseCaseUnitTest extends TestCase
{
    public function testListCastMembersEmpty()
    {
        $mockPagination = $this->mockPagination();

        $mockRepository = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $mockRepository
            ->shouldReceive('paginate')
            ->once()
            ->andReturn($mockPagination);

        $mockInputDto = Mockery::mock(ListCastMembersInputDto::class, [

        ]);

        $useCase = new ListCastMembersUseCase($mockRepository);
        $response = $useCase->execute($mockInputDto);

        $this->assertCount(0, $response->items());
        $this->assertInstanceOf(PaginationInterface::class, $response);
    }

    protected function mockPagination(array $items = [])
    {
        $mockPagination = Mockery::mock(stdClass::class, PaginationInterface::class);
        $mockPagination->shouldReceive('items')->andReturn($items);
        $mockPagination->shouldReceive('total')->andReturn(0);
        $mockPagination->shouldReceive('currentPage')->andReturn(0);
        $mockPagination->shouldReceive('lastPage')->andReturn(0);
        $mockPagination->shouldReceive('firstPage')->andReturn(0);
        $mockPagination->shouldReceive('perPage')->andReturn(0);
        $mockPagination->shouldReceive('to')->andReturn(0);
        $mockPagination->shouldReceive('from')->andReturn(0);

        return $mockPagination;
    }

    public function testListCastMembers()
    {
        $register = new stdClass();
        $register->id = 'id';
        $register->name = 'name';
        $register->type = CastMemberType::ACTOR;
        $register->created_at = 'created_at';
        $register->updated_at = 'updated_at';
        $register->deleted_at = 'deleted_at';
        $mockPagination = $this->mockPagination([
            $register,
        ]);

        $mockRepository = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $mockRepository
            ->shouldReceive('paginate')
            ->andReturn($mockPagination);

        $mockInputDto = Mockery::mock(ListCastMembersInputDto::class, [

        ]);

        $useCase = new ListCastMembersUseCase($mockRepository);
        $response = $useCase->execute($mockInputDto);

        $this->assertCount(1, $response->items());
        $this->assertInstanceOf(stdClass::class, $response->items()[0]);
        $this->assertInstanceOf(PaginationInterface::class, $response);
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
