<?php

namespace Tests\Unit\UseCase\Category;

use Core\Domain\Entity\Category;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\Category\CreateCategoryUseCase;
use Core\UseCase\DTO\Category\CategoryCreateInputDto;
use Core\UseCase\DTO\Category\CategoryCreateOutputDto;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

class CreateCategoryUseCaseUnitTest extends TestCase
{
    public function testCreateNewCategory()
    {
        $uuid = Uuid::uuid4()->toString();
        $categoryName = 'New Cat';
        $mockEntity = Mockery::mock(Category::class, [
            $uuid,
            $categoryName
        ]);
        $mockEntity->shouldReceive('id')->andReturn($uuid);

        $mockRepository = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $mockRepository->shouldReceive('insert')->andReturn($mockEntity);

        $mockInputDto = Mockery::mock(CategoryCreateInputDto::class, [
            $categoryName
        ]);

        $useCase = new CreateCategoryUseCase($mockRepository);
        $response = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(CategoryCreateOutputDto::class, $response);
        $this->assertEquals($categoryName, $response->name);
        $this->assertEquals('', $response->description);

        /**
         * Spies
         */
        $spy = Mockery::spy(stdClass::class, CategoryRepositoryInterface::class);
        $spy->shouldReceive('insert')->andReturn($mockEntity);

        $useCase = new CreateCategoryUseCase($spy);
        $useCase->execute($mockInputDto);

        $spy->shouldHaveReceived('insert');
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}