<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCastMemberRequest;
use App\Http\Requests\UpdateCastMemberRequest;
use App\Http\Resources\CastMemberResource;
use Core\UseCase\CastMember\CreateCastMemberUseCase;
use Core\UseCase\CastMember\DeleteCastMemberUseCase;
use Core\UseCase\CastMember\ListCastMembersUseCase;
use Core\UseCase\CastMember\ListCastMemberUseCase;
use Core\UseCase\CastMember\UpdateCastMemberUseCase;
use Core\UseCase\DTO\CastMember\CastMemberInputDto;
use Core\UseCase\DTO\CastMember\CreateCastMember\CastMemberCreateInputDto;
use Core\UseCase\DTO\CastMember\ListCastMembers\ListCastMembersInputDto;
use Core\UseCase\DTO\CastMember\UpdateCastMember\CastMemberUpdateInputDto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CastMemberController extends Controller
{
    public function index(Request $request, ListCastMembersUseCase $useCase)
    {
        $response = $useCase->execute(
            input: new ListCastMembersInputDto(
                filter: $request->get('filter', ''),
                order: $request->get('order', 'DESC'),
                page: (int)$request->get('page'),
                totalPage: (int)$request->get('total_page', 15)
            )
        );

        return CastMemberResource::collection(collect($response->items()))->additional([
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

    public function store(StoreCastMemberRequest $request, CreateCastMemberUseCase $useCase)
    {
        $response = $useCase->execute(
            input: new CastMemberCreateInputDto(
                name: $request->name,
                type: $request->type
            )
        );

        return (new CastMemberResource($response))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show($id, ListCastMemberUseCase $useCase)
    {
        $response = $useCase->execute(
            input: new CastMemberInputDto(
                id: $id
            )
        );

        return (new CastMemberResource($response))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    public function update($id, UpdateCastMemberRequest $request, UpdateCastMemberUseCase $useCase)
    {
        $response = $useCase->execute(
            input: new CastMemberUpdateInputDto(
                id: $id,
                name: $request->name
            )
        );

        return (new CastMemberResource($response))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    public function destroy($id, DeleteCastMemberUseCase $useCase)
    {
        $useCase->execute(
            input: new CastMemberInputDto(
                id: $id
            )
        );

        return response()->noContent();
    }
}
