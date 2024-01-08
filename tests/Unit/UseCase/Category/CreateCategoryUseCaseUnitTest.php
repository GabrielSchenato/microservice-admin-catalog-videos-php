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
        $responseUseCase = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(CategoryCreateOutputDto::class, $responseUseCase);
        $this->assertEquals($categoryName, $responseUseCase->name);
        $this->assertEquals('', $responseUseCase->description);

        /**
         * Spies
         */

        $spy = Mockery::spy(stdClass::class, CategoryRepositoryInterface::class);
        $spy->shouldReceive('insert')->andReturn($mockEntity);

        $useCase = new CreateCategoryUseCase($spy);
        $responseUseCase = $useCase->execute($mockInputDto);

        $spy->shouldHaveReceived('insert');

        Mockery::close();
    }
}