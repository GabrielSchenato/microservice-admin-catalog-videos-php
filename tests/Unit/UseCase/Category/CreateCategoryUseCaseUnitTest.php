<?php

namespace Tests\Unit\UseCase\Category;

use Core\Domain\Entity\CategoryEntity;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\Category\CreateCategoryUseCase;
use Core\UseCase\DTO\Category\CreateCategory\CategoryCreateInputDto;
use Core\UseCase\DTO\Category\CreateCategory\CategoryCreateOutputDto;
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
        $mockEntity = Mockery::mock(CategoryEntity::class, [
            $uuid,
            $categoryName
        ]);
        $mockEntity->shouldReceive('id')->andReturn($uuid);
        $mockEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));

        $mockRepository = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $mockRepository
            ->shouldReceive('insert')
            ->once()
            ->andReturn($mockEntity);

        $mockInputDto = Mockery::mock(CategoryCreateInputDto::class, [
            $categoryName
        ]);

        $useCase = new CreateCategoryUseCase($mockRepository);
        $response = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(CategoryCreateOutputDto::class, $response);
        $this->assertEquals($categoryName, $response->name);
        $this->assertEquals('', $response->description);
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}