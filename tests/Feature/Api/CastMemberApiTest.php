<?php

namespace Tests\Feature\Api;

use App\Models\CastMember;
use Core\Domain\Enum\CastMemberType;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\Traits\WithoutMiddlewareTrait;

class CastMemberApiTest extends TestCase
{
    use WithoutMiddlewareTrait;

    protected string $endpoint = '/api/cast_members';

    public function testListEmptyAllCastMembers(): void
    {
        $response = $this->getJson($this->endpoint);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(0, 'data');
    }

    public function testListAllCastMembers(): void
    {
        CastMember::factory()->count(30)->create();

        $response = $this->getJson($this->endpoint);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'meta' => [
                'total',
                'current_page',
                'last_page',
                'first_page',
                'per_page',
                'to',
                'from',
            ]
        ]);
        $response->assertJsonCount(15, 'data');
    }

    public function testListPaginateCastMembers(): void
    {
        CastMember::factory()->count(25)->create();

        $response = $this->getJson("$this->endpoint?page=2");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'meta' => [
                'total',
                'current_page',
                'last_page',
                'first_page',
                'per_page',
                'to',
                'from',
            ]
        ]);
        $this->assertEquals(2, $response['meta']['current_page']);
        $this->assertEquals(25, $response['meta']['total']);
        $response->assertJsonCount(10, 'data');
    }

    public function testListPaginateCastMembersWithFilter(): void
    {
        $name = 'Teste';
        CastMember::factory()->count(10)->create();
        CastMember::factory()->count(10)->create([
            'name' => $name
        ]);

        $response = $this->getJson("$this->endpoint?filter={$name}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(10, 'data');
    }

    public function testListCastMemberNotFound(): void
    {
        $response = $this->getJson("$this->endpoint/fake_value");

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testListCastMember(): void
    {
        $category = CastMember::factory()->create();

        $response = $this->getJson("$this->endpoint/{$category->id}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'type',
                'created_at'
            ]
        ]);
        $this->assertEquals($category->id, $response['data']['id']);
    }

    public function testValidationsStore(): void
    {
        $data = [];

        $response = $this->postJson($this->endpoint, $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'name',
                'type'
            ]
        ]);
    }

    public function testStore(): void
    {
        $data = [
            'name' => 'Teste',
            'type' => CastMemberType::DIRECTOR->value
        ];

        $response = $this->postJson($this->endpoint, $data);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'type',
                'created_at'
            ]
        ]);


        $this->assertEquals($data['name'], $response['data']['name']);
        $this->assertEquals($data['type'], $response['data']['type']);
    }

    public function testNotFoundUpdate(): void
    {
        $response = $this->putJson("$this->endpoint/fake_value", [
            'name' => 'Teste'
        ]);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testValidationsUpdate(): void
    {
        $data = [
            'name' => '',
            'type' => ''
        ];

        $response = $this->putJson("$this->endpoint/fake_value", $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'name'
            ]
        ]);
    }

    public function testUpdate(): void
    {
        $category = CastMember::factory()->create();

        $data = [
            'name' => 'Teste'
        ];

        $response = $this->putJson("{$this->endpoint}/{$category->id}", $data);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'type',
                'created_at'
            ]
        ]);

        $this->assertEquals($data['name'], $response['data']['name']);
        $this->assertDatabaseHas('cast_members', [
            'name' => $data['name']
        ]);
    }

    public function testNotFoundDelete(): void
    {
        $response = $this->deleteJson("$this->endpoint/fake_value");

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testDelete(): void
    {
        $category = CastMember::factory()->create();

        $response = $this->deleteJson("$this->endpoint/$category->id");

        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertSoftDeleted('cast_members', [
            'id' => $category->id
        ]);
    }

}
