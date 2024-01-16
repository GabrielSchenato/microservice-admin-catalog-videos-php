<?php

namespace Tests\Unit\UseCase\Genre;

use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;
use Core\UseCase\DTO\Genre\ListGenres\ListGenresInputDto;
use Core\UseCase\Genre\ListGenresUseCase;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;

class ListGenresUseCaseUnitTest extends TestCase
{
    public function testListGenresEmpty()
    {
        $mockPagination = $this->mockPagination();

        $mockRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockRepository
            ->shouldReceive('paginate')
            ->once()
            ->andReturn($mockPagination);

        $mockInputDto = Mockery::mock(ListGenresInputDto::class, [
            'teste', 'desc', 1, 15,
        ]);

        $useCase = new ListGenresUseCase($mockRepository);
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

    public function testListGenres()
    {
        $register = new stdClass();
        $register->id = 'id';
        $register->name = 'name';
        $register->is_active = true;
        $register->created_at = 'created_at';
        $register->updated_at = 'updated_at';
        $register->deleted_at = 'deleted_at';
        $mockPagination = $this->mockPagination([
            $register,
        ]);

        $mockRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockRepository
            ->shouldReceive('paginate')
            ->andReturn($mockPagination);

        $mockInputDto = Mockery::mock(ListGenresInputDto::class, [

        ]);

        $useCase = new ListGenresUseCase($mockRepository);
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
