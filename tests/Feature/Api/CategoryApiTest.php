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
        $response = $this->getJson("$this->endpoint/fale_value");

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

}
