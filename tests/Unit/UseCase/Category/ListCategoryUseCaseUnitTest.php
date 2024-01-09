<?php

namespace Tests\Unit\UseCase\Category;

use Core\Domain\Entity\Category;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\Category\ListCategoryUseCase;
use Core\UseCase\DTO\Category\CategoryInputDto;
use Core\UseCase\DTO\Category\CategoryOutputDto;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

class ListCategoryUseCaseUnitTest extends TestCase
{
    public function testGetById()
    {
        $uuid = Uuid::uuid4()->toString();
        $categoryName = 'New Cat';
        $mockEntity = Mockery::mock(Category::class, [
            $uuid,
            $categoryName
        ]);
        $mockEntity->shouldReceive('id')->andReturn($uuid);

        $mockRepository = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $mockRepository
            ->shouldReceive('findById')
            ->with($uuid)
            ->andReturn($mockEntity);

        $mockInputDto = Mockery::mock(CategoryInputDto::class, [
            $uuid
        ]);

        $useCase = new ListCategoryUseCase($mockRepository);
        $response = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(CategoryOutputDto::class, $response);
        $this->assertEquals($categoryName, $response->name);
        $this->assertEquals('', $response->description);

        /**
         * Spies
         */
        $spy = Mockery::spy(stdClass::class, CategoryRepositoryInterface::class);
        $spy->shouldReceive('findById')
            ->with($uuid)
            ->andReturn($mockEntity);

        $useCase = new ListCategoryUseCase($spy);
        $response = $useCase->execute($mockInputDto);

        $spy->shouldHaveReceived('findById');
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}