<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\Traits\WithoutMiddlewareTrait;

class CategoryApiTest extends TestCase
{
    use WithoutMiddlewareTrait;

    protected string $endpoint = '/api/categories';

    public function testListEmptyAllCategories(): void
    {
        $response = $this->getJson($this->endpoint);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(0, 'data');
    }

    public function testListAllCategories(): void
    {
        Category::factory()->count(30)->create();

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

    public function testListPaginateCategories(): void
    {
        Category::factory()->count(25)->create();

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

    public function testListCategoryNotFound(): void
    {
        $response = $this->getJson("$this->endpoint/fake_value");

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testListCategory(): void
    {
        $category = Category::factory()->create();

        $response = $this->getJson("$this->endpoint/{$category->id}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'is_active',
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
                'name'
            ]
        ]);
    }

    public function testStore(): void
    {
        $data = [
            'name' => 'Teste'
        ];

        $response = $this->postJson($this->endpoint, $data);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'is_active',
                'created_at'
            ]
        ]);

        $data = [
            'name' => 'Teste',
            'description' => 'Teste',
            'is_active' => false
        ];

        $response = $this->postJson($this->endpoint, $data);

        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertEquals($data['name'], $response['data']['name']);
        $this->assertEquals($data['description'], $response['data']['description']);
        $this->assertFalse($response['data']['is_active']);
        $this->assertDatabaseHas('categories', [
            'id' => $response['data']['id'],
            'is_active' => false
        ]);
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
            'name' => ''
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
        $category = Category::factory()->create();

        $data = [
            'name' => 'Teste'
        ];

        $response = $this->putJson("{$this->endpoint}/{$category->id}", $data);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'is_active',
                'created_at'
            ]
        ]);

        $this->assertEquals($data['name'], $response['data']['name']);
        $this->assertDatabaseHas('categories', [
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
        $category = Category::factory()->create();

        $response = $this->deleteJson("$this->endpoint/$category->id");

        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertSoftDeleted('categories', [
            'id' => $category->id
        ]);
    }

}
