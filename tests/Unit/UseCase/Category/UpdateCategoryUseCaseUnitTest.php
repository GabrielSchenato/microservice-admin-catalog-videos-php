<?php

namespace Tests\Unit\UseCase\Category;

use Core\Domain\Entity\Category;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\Category\UpdateCategoryUseCase;
use Core\UseCase\DTO\Category\CategoryUpdateInputDto;
use Core\UseCase\DTO\Category\CategoryUpdateOutputDto;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

class UpdateCategoryUseCaseUnitTest extends TestCase
{
    public function testRenameCategory()
    {
        $categoryNewName = 'New Cat';
        $uuid = Uuid::uuid4()->toString();
        $categoryName = 'Old Cat';
        $categoryDescription = 'Old Description';
        $mockEntity = Mockery::mock(Category::class, [
            $uuid,
            $categoryName,
            $categoryDescription
        ]);
        $mockEntity->shouldReceive('update');

        $mockRepository = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $mockRepository->shouldReceive('findById')->andReturn($mockEntity);
        $mockRepository->shouldReceive('update')->andReturn($mockEntity);

        $mockInputDto = Mockery::mock(CategoryUpdateInputDto::class, [
            $uuid,
            $categoryNewName
        ]);

        $useCase = new UpdateCategoryUseCase($mockRepository);
        $response = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(CategoryUpdateOutputDto::class, $response);

        /**
         * Spies
         */
        $spy = Mockery::spy(stdClass::class, CategoryRepositoryInterface::class);
        $spy->shouldReceive('findById')->andReturn($mockEntity);
        $spy->shouldReceive('update')->andReturn($mockEntity);

        $mockInputDto = Mockery::mock(CategoryUpdateInputDto::class, [
            $uuid,
            $categoryNewName
        ]);

        $useCase = new UpdateCategoryUseCase($spy);
        $response = $useCase->execute($mockInputDto);
        $spy->shouldHaveReceived('findById');
        $spy->shouldHaveReceived('update');
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}