<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use Core\UseCase\Category\CreateCategoryUseCase;
use Core\UseCase\Category\DeleteCategoryUseCase;
use Core\UseCase\Category\ListCategoriesUseCase;
use Core\UseCase\Category\ListCategoryUseCase;
use Core\UseCase\Category\UpdateCategoryUseCase;
use Core\UseCase\DTO\Category\CategoryInputDto;
use Core\UseCase\DTO\Category\CreateCategory\CategoryCreateInputDto;
use Core\UseCase\DTO\Category\ListCategories\ListCategoriesInputDto;
use Core\UseCase\DTO\Category\UpdateCategory\CategoryUpdateInputDto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    public function index(Request $request, ListCategoriesUseCase $useCase)
    {
        $response = $useCase->execute(
            input: new ListCategoriesInputDto(
                filter: $request->get('filter', ''),
                order: $request->get('order', 'DESC'),
                page: (int)$request->get('page'),
                totalPage: (int)$request->get('total_page', 15)
            )
        );

        return CategoryResource::collection(collect($response->items()))->additional([
            'meta' => [
                'total' => $response->total(),
                'current_page' => $response->currentPage(),
                'last_page' => $response->lastPage(),
                'first_page' => $response->firstPage(),
                'per_page' => $response->perPage(),
                'to' => $response->to(),
                'from' => $response->from(),
            ]
        ]);
    }

    public function store(StoreCategoryRequest $request, CreateCategoryUseCase $useCase)
    {
        $response = $useCase->execute(
            input: new CategoryCreateInputDto(
                name: $request->name,
                description: $request->description ?? '',
                isActive: (bool)$request->is_active ?? true
            )
        );

        return (new CategoryResource($response))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show($id, ListCategoryUseCase $useCase)
    {
        $response = $useCase->execute(
            input: new CategoryInputDto(
                id: $id
            )
        );

        return (new CategoryResource($response))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    public function update($id, UpdateCategoryRequest $request, UpdateCategoryUseCase $useCase)
    {
        $response = $useCase->execute(
            input: new CategoryUpdateInputDto(
                id: $id,
                name: $request->name
            )
        );

        return (new CategoryResource($response))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    public function destroy($id, DeleteCategoryUseCase $useCase)
    {
        $useCase->execute(
            input: new CategoryInputDto(
                id: $id
            )
        );

        return response()->noContent();
    }
}
