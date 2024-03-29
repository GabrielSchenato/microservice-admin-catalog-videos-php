<?php

namespace App\Http\Controllers\Api;

use App\Adapters\ApiAdapter;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGenreRequest;
use App\Http\Requests\UpdateGenreRequest;
use Core\UseCase\DTO\Genre\CreateGenre\GenreCreateInputDto;
use Core\UseCase\DTO\Genre\GenreInputDto;
use Core\UseCase\DTO\Genre\ListGenres\ListGenresInputDto;
use Core\UseCase\DTO\Genre\UpdateGenre\GenreUpdateInputDto;
use Core\UseCase\Genre\CreateGenreUseCase;
use Core\UseCase\Genre\DeleteGenreUseCase;
use Core\UseCase\Genre\ListGenresUseCase;
use Core\UseCase\Genre\ListGenreUseCase;
use Core\UseCase\Genre\UpdateGenreUseCase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GenreController extends Controller
{
    public function index(Request $request, ListGenresUseCase $useCase)
    {
        $response = $useCase->execute(
            input: new ListGenresInputDto(
                filter: $request->get('filter', ''),
                order: $request->get('order', 'DESC'),
                page: (int) $request->get('page'),
                totalPage: (int) $request->get('total_page', 15)
            )
        );

        return (new ApiAdapter($response))->toJson();
    }

    public function store(StoreGenreRequest $request, CreateGenreUseCase $useCase)
    {
        $response = $useCase->execute(
            input: new GenreCreateInputDto(
                name: $request->name,
                categoriesId: $request->categories_ids ?? [],
                isActive: (bool) $request->is_active ?? true
            )
        );

        return ApiAdapter::json($response, Response::HTTP_CREATED);
    }

    public function show($id, ListGenreUseCase $useCase)
    {
        $response = $useCase->execute(
            input: new GenreInputDto(
                id: $id
            )
        );

        return ApiAdapter::json($response);
    }

    public function update($id, UpdateGenreRequest $request, UpdateGenreUseCase $useCase)
    {
        $response = $useCase->execute(
            input: new GenreUpdateInputDto(
                id: $id,
                name: $request->name,
                categoriesId: $request->categories_ids ?? [],
            )
        );

        return ApiAdapter::json($response);
    }

    public function destroy($id, DeleteGenreUseCase $useCase)
    {
        $useCase->execute(
            input: new GenreInputDto(
                id: $id
            )
        );

        return response()->noContent();
    }
}
