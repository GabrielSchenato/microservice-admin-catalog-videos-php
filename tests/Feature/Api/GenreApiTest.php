<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Genre;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class GenreApiTest extends TestCase
{
    protected string $endpoint = '/api/genres';

    public function testListEmptyAllGenres(): void
    {
        $response = $this->getJson($this->endpoint);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(0, 'data');
    }

    public function testListAllGenres(): void
    {
        Genre::factory()->count(30)->create();

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

    public function testListPaginateGenres(): void
    {
        Genre::factory()->count(25)->create();

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

    public function testListGenreNotFound(): void
    {
        $response = $this->getJson("$this->endpoint/fake_value");

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testListGenre(): void
    {
        $genre = Genre::factory()->create();

        $response = $this->getJson("$this->endpoint/{$genre->id}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'is_active',
                'created_at'
            ]
        ]);
        $this->assertEquals($genre->id, $response['data']['id']);
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
        $categories = Category::factory()->count(10)->create();
        $data = [
            'name' => 'Teste',
            'categories_ids' => $categories->pluck('id')->toArray()
        ];

        $response = $this->postJson($this->endpoint, $data);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'is_active',
                'created_at'
            ]
        ]);

        $data = [
            'name' => 'Teste',
            'is_active' => false
        ];

        $response = $this->postJson($this->endpoint, $data);

        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertEquals($data['name'], $response['data']['name']);
        $this->assertFalse($response['data']['is_active']);
        $this->assertDatabaseHas('genres', [
            'id' => $response['data']['id'],
            'is_active' => false
        ]);
        $this->assertDatabaseCount('category_genre', 10);
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
        $genre = Genre::factory()->create();

        $data = [
            'name' => 'Teste'
        ];

        $response = $this->putJson("{$this->endpoint}/{$genre->id}", $data);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'is_active',
                'created_at'
            ]
        ]);

        $this->assertEquals($data['name'], $response['data']['name']);
        $this->assertDatabaseHas('genres', [
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
        $genre = Genre::factory()->create();

        $response = $this->deleteJson("$this->endpoint/$genre->id");

        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertSoftDeleted('genres', [
            'id' => $genre->id
        ]);
    }

}
