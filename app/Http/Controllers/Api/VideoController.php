<?php

namespace App\Http\Controllers\Api;

use App\Adapters\ApiAdapter;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVideoRequest;
use App\Http\Requests\UpdateVideoRequest;
use Core\Domain\Enum\Rating;
use Core\UseCase\Video\Create\CreateVideoUseCase;
use Core\UseCase\Video\Create\DTO\VideoCreateInputDto;
use Core\UseCase\Video\Delete\DeleteVideoUseCase;
use Core\UseCase\Video\Delete\DTO\VideoDeleteInputDto;
use Core\UseCase\Video\List\DTO\VideoListInputDto;
use Core\UseCase\Video\List\ListVideoUseCase;
use Core\UseCase\Video\ListPaginate\DTO\VideosListInputDto;
use Core\UseCase\Video\ListPaginate\ListVideosUseCase;
use Core\UseCase\Video\Update\DTO\VideoUpdateInputDto;
use Core\UseCase\Video\Update\UpdateVideoUseCase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VideoController extends Controller
{
    public function index(Request $request, ListVideosUseCase $useCase)
    {
        $response = $useCase->execute(
            input: new VideosListInputDto(
                filter: $request->filter ?? '',
                order: $request->get('order', 'DESC'),
                page: (int) $request->get('page', 1),
                totalPage: (int) $request->get('total_page', 15)
            )
        );

        return (new ApiAdapter($response))->toJson();
    }

    public function store(StoreVideoRequest $request, CreateVideoUseCase $useCase)
    {
        $response = $useCase->execute(
            input: new VideoCreateInputDto(
                title: $request->title,
                description: $request->description,
                yearLaunched: $request->year_launched,
                duration: $request->duration,
                opened: $request->opened,
                rating: Rating::from($request->rating),
                categoriesId: $request->categories,
                genresId: $request->genres,
                castMembersId: $request->cast_members,
                videoFile: getArrayFile($request->file('video_file')),
                trailerFile: getArrayFile($request->file('trailer_file')),
                thumbFile: getArrayFile($request->file('thumb_file')),
                thumbHalf: getArrayFile($request->file('thumb_half_file')),
                bannerFile: getArrayFile($request->file('banner_file')),
            )
        );

        return ApiAdapter::json($response, Response::HTTP_CREATED);
    }

    public function show($id, ListVideoUseCase $useCase)
    {
        $response = $useCase->execute(
            input: new VideoListInputDto(
                id: $id
            )
        );

        return ApiAdapter::json($response);
    }

    public function update($id, UpdateVideoRequest $request, UpdateVideoUseCase $useCase)
    {
        $response = $useCase->execute(
            input: new VideoUpdateInputDto(
                id: $id,
                title: $request->title,
                description: $request->description,
                categoriesId: $request->categories,
                genresId: $request->genres,
                castMembersId: $request->cast_members,
                videoFile: getArrayFile($request->file('video_file')),
                trailerFile: getArrayFile($request->file('trailer_file')),
                thumbFile: getArrayFile($request->file('thumb_file')),
                thumbHalf: getArrayFile($request->file('thumb_half_file')),
                bannerFile: getArrayFile($request->file('banner_file')),
            )
        );

        return ApiAdapter::json($response);
    }

    public function destroy($id, DeleteVideoUseCase $useCase)
    {
        $useCase->execute(
            input: new VideoDeleteInputDto(
                id: $id
            )
        );

        return response()->noContent();
    }
}
