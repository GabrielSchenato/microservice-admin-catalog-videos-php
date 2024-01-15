<?php

namespace Api;

use App\Models\Video;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class VideoApiTest extends TestCase
{
    protected string $endpoint = '/api/videos';

    protected array $serializedFields = [
        'id',
        'title',
        'description',
        'year_launched',
        'opened',
        'rating',
        'duration',
        'created_at',
    ];

    public function testListEmptyAllVideos(): void
    {
        $response = $this->getJson($this->endpoint);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(0, 'data');
    }

    /**
     * @dataProvider dataProviderPagination
     */
    public function testListAllVideos(
        int    $total,
        int    $totalCurrentPage,
        int    $page = 1,
        int    $perPage = 15,
        string $filter = '',
    ): void
    {
        Video::factory()->count($total)->create();

        if ($filter) {
            Video::factory()->count($total)->create([
                'title' => $filter,
            ]);
        }

        $params = http_build_query([
            'page' => $page,
            'total_page' => $perPage,
            'order' => 'DESC',
            'filter' => $filter,
        ]);

        $response = $this->getJson("$this->endpoint?$params");

        $response->assertOk();
        $response->assertJsonCount($totalCurrentPage, 'data');
        $response->assertJsonPath('meta.current_page', $page);
        $response->assertJsonPath('meta.per_page', $perPage);
        $response->assertJsonStructure([
            'data' => [
                '*' => $this->serializedFields,
            ],
            'meta' => [
                'total',
                'current_page',
                'last_page',
                'first_page',
                'per_page',
                'to',
                'from',
            ],
        ]);
    }

    public static function dataProviderPagination(): array
    {
        return [
            'test empty' => [
                'total' => 0,
                'totalCurrentPage' => 0,
                'page' => 1,
                'perPage' => 15,
            ],
            'test with total two pages' => [
                'total' => 20,
                'totalCurrentPage' => 15,
                'page' => 1,
                'perPage' => 15,
            ],
            'test page two' => [
                'total' => 20,
                'totalCurrentPage' => 5,
                'page' => 2,
                'perPage' => 15,
            ],
            'test page four' => [
                'total' => 40,
                'totalCurrentPage' => 10,
                'page' => 4,
                'perPage' => 10,
            ],
            'test with filter' => [
                'total' => 10,
                'totalCurrentPage' => 10,
                'page' => 1,
                'perPage' => 10,
                'filter' => 'test',
            ],
        ];
    }

    public function testListVideoNotFound(): void
    {
        $response = $this->getJson("$this->endpoint/fake_value");

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testListVideo(): void
    {
        $video = Video::factory()->create();

        $response = $this->getJson("$this->endpoint/{$video->id}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => $this->serializedFields,
        ]);
        $this->assertEquals($video->id, $response['data']['id']);
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
        $this->assertDatabaseHas('videos', [
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
        $video = Video::factory()->create();

        $data = [
            'name' => 'Teste'
        ];

        $response = $this->putJson("{$this->endpoint}/{$video->id}", $data);

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
        $this->assertDatabaseHas('videos', [
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
        $video = Video::factory()->create();

        $response = $this->deleteJson("$this->endpoint/$video->id");

        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertSoftDeleted('videos', [
            'id' => $video->id
        ]);
    }

}
