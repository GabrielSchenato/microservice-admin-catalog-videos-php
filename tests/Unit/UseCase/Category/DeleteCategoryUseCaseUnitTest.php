<?php

namespace Tests\Unit\UseCase\Category;

use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\Category\DeleteCategoryUseCase;
use Core\UseCase\DTO\Category\CategoryInputDto;
use Core\UseCase\DTO\Category\DeleteCategory\CategoryDeleteOutputDto;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

class DeleteCategoryUseCaseUnitTest extends TestCase
{
    public function testDelete()
    {
        $uuid = Uuid::uuid4()->toString();
        $mockRepository = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $mockRepository
            ->shouldReceive('delete')
            ->once()
            ->andReturn(true);

        $mockInputDto = Mockery::mock(CategoryInputDto::class, [
            $uuid,
        ]);

        $useCase = new DeleteCategoryUseCase($mockRepository);
        $response = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(CategoryDeleteOutputDto::class, $response);
        $this->assertTrue($response->success);
    }

    public function testDeleteFalse()
    {
        $uuid = Uuid::uuid4()->toString();
        $mockRepository = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $mockRepository->shouldReceive('delete')->andReturn(false);

        $mockInputDto = Mockery::mock(CategoryInputDto::class, [
            $uuid,
        ]);

        $useCase = new DeleteCategoryUseCase($mockRepository);
        $response = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(CategoryDeleteOutputDto::class, $response);
        $this->assertFalse($response->success);
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
