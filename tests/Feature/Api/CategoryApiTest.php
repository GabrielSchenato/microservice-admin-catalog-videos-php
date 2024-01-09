<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CategoryApiTest extends TestCase
{
    protected string $endpoint = '/api/categories';

    public function testListEmptyAllCategories(): void
    {
        $response = $this->getJson($this->endpoint);

        $response->assertStatus(Response::HTTP_OK);
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
    }

    public function testListPaginateCategories(): void
    {
        Category::factory()->count(30)->create();

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
        $this->assertEquals(30, $response['meta']['total']);
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

}
