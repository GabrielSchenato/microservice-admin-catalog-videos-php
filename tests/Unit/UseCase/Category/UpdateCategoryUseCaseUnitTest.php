<?php

namespace Tests\Unit\UseCase\Category;

use Core\Domain\Entity\CategoryEntity;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\Category\UpdateCategoryUseCase;
use Core\UseCase\DTO\Category\UpdateCategory\CategoryUpdateInputDto;
use Core\UseCase\DTO\Category\UpdateCategory\CategoryUpdateOutputDto;
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
        $mockEntity = Mockery::mock(CategoryEntity::class, [
            $uuid,
            $categoryName,
            $categoryDescription
        ]);
        $mockEntity->shouldReceive('update');
        $mockEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));

        $mockRepository = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $mockRepository
            ->shouldReceive('findById')
            ->once()
            ->with($uuid)
            ->andReturn($mockEntity);
        $mockRepository
            ->shouldReceive('update')
            ->once()
            ->andReturn($mockEntity);

        $mockInputDto = Mockery::mock(CategoryUpdateInputDto::class, [
            $uuid,
            $categoryNewName
        ]);

        $useCase = new UpdateCategoryUseCase($mockRepository);
        $response = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(CategoryUpdateOutputDto::class, $response);
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}