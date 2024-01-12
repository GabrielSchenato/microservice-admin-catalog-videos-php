<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreCastMemberRequest;
use App\Http\Requests\UpdateCastMemberRequest;
use App\Models\CastMember;
use App\Repositories\Eloquent\CastMemberEloquentRepository;
use Core\Domain\Enum\CastMemberType;
use Core\UseCase\CastMember\CreateCastMemberUseCase;
use Core\UseCase\CastMember\DeleteCastMemberUseCase;
use Core\UseCase\CastMember\ListCastMembersUseCase;
use Core\UseCase\CastMember\ListCastMemberUseCase;
use Core\UseCase\CastMember\UpdateCastMemberUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CastMemberControllerTest extends TestCase
{
    protected CastMemberEloquentRepository $repository;
    protected CastMemberController $controller;

    protected function setUp(): void
    {
        $this->repository = new CastMemberEloquentRepository(new CastMember());
        $this->controller = new CastMemberController();

        parent::setUp();
    }

    public function testIndex(): void
    {
        $useCase = new ListCastMembersUseCase($this->repository);
        $response = $this->controller->index(new Request(), $useCase);

        $this->assertInstanceOf(AnonymousResourceCollection::class, $response);
        $this->assertArrayHasKey('meta', $response->additional);
    }

    public function testStore(): void
    {
        $useCase = new CreateCastMemberUseCase($this->repository);

        $request = new StoreCastMemberRequest();
        $request->headers->set('Content-type', 'application/json');
        $request->setJson(new InputBag([
            'name' => 'Teste',
            'type' => CastMemberType::DIRECTOR->value
        ]));

        $response = $this->controller->store($request, $useCase);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_CREATED, $response->status());
    }

    public function testShow(): void
    {
        $castMember = CastMember::factory()->create();

        $response = $this->controller->show(
            id: $castMember->id,
            useCase: new ListCastMemberUseCase($this->repository)
        );

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->status());
    }

    public function testUpdate(): void
    {
        $castMember = CastMember::factory()->create();

        $useCase = new UpdateCastMemberUseCase($this->repository);

        $request = new UpdateCastMemberRequest();
        $request->headers->set('Content-type', 'application/json');
        $request->setJson(new InputBag([
            'name' => 'Teste'
        ]));

        $response = $this->controller->update($castMember->id, $request, $useCase);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->status());
        $this->assertDatabaseHas('cast_members', [
            'name' => 'Teste'
        ]);
    }

    public function testDestroy(): void
    {
        $castMember = CastMember::factory()->create();

        $useCase = new DeleteCastMemberUseCase($this->repository);

        $response = $this->controller->destroy($castMember->id, $useCase);

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->status());
    }
}
