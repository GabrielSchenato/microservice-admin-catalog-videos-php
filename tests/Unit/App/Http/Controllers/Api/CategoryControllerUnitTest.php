<?php

namespace Tests\Unit\App\Http\Controllers\Api;

use App\Http\Controllers\Api\CategoryController;
use Core\UseCase\Category\ListCategoriesUseCase;
use Illuminate\Http\Request;
use Mockery;
use PHPUnit\Framework\TestCase;
use Tests\Unit\UseCase\UseCaseTrait;

class CategoryControllerUnitTest extends TestCase
{
    use UseCaseTrait;

    protected $mockPagination;

    public function testIndex(): void
    {
        $mockRequest = Mockery::mock(Request::class);
        $mockRequest->shouldReceive('get')->andReturn('');

        $mockUseCase = Mockery::mock(ListCategoriesUseCase::class);
        $mockUseCase->shouldReceive('execute')->once()->andReturn($this->mockPagination());

        $controller = new CategoryController();
        $response = $controller->index($mockRequest, $mockUseCase);

        $this->assertIsObject($response->resource);
        $this->assertArrayHasKey('meta', $response->additional);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
