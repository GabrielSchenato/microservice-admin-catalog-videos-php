<?php

namespace Tests\Feature\App\Http\Controllers\Api;

use App\Http\Controllers\Api\CategoryController;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use Core\UseCase\Category\CreateCategoryUseCase;
use Core\UseCase\Category\DeleteCategoryUseCase;
use Core\UseCase\Category\ListCategoriesUseCase;
use Core\UseCase\Category\ListCategoryUseCase;
use Core\UseCase\Category\UpdateCategoryUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    protected CategoryEloquentRepository $repository;
    protected CategoryController $controller;

    protected function setUp(): void
    {
        $this->repository = new CategoryEloquentRepository(new Category());
        $this->controller = new CategoryController();

        parent::setUp();
    }

    public function testIndex(): void
    {
        $useCase = new ListCategoriesUseCase($this->repository);
        $response = $this->controller->index(new Request(), $useCase);

        $this->assertInstanceOf(AnonymousResourceCollection::class, $response);
        $this->assertArrayHasKey('meta', $response->additional);
    }

    public function testStore(): void
    {
        $useCase = new CreateCategoryUseCase($this->repository);

        $request = new StoreCategoryRequest();
        $request->headers->set('Content-type', 'application/json');
        $request->setJson(new InputBag([
            'name' => 'Teste'
        ]));

        $response = $this->controller->store($request, $useCase);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_CREATED, $response->status());
    }

    public function testShow(): void
    {
        $category = Category::factory()->create();

        $response = $this->controller->show(
            id: $category->id,
            useCase: new ListCategoryUseCase($this->repository)
        );

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->status());
    }

    public function testUpdate(): void
    {
        $category = Category::factory()->create();

        $useCase = new UpdateCategoryUseCase($this->repository);

        $request = new UpdateCategoryRequest();
        $request->headers->set('Content-type', 'application/json');
        $request->setJson(new InputBag([
            'name' => 'Teste'
        ]));

        $response = $this->controller->update($category->id, $request, $useCase);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->status());
        $this->assertDatabaseHas('categories', [
            'name' => 'Teste'
        ]);
    }

    public function testDestroy(): void
    {
        $category = Category::factory()->create();

        $useCase = new DeleteCategoryUseCase($this->repository);

        $response = $this->controller->destroy($category->id, $useCase);

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->status());
    }
}
