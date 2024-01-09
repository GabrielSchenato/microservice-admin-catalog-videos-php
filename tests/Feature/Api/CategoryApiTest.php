<?php

namespace Tests\Feature\Api;

use Tests\TestCase;

class CategoryApiTest extends TestCase
{
    protected string $endpoint = '/api/categories';

    public function testListEmptyAllCategories(): void
    {
        $response = $this->getJson($this->endpoint);

        $response->assertStatus(200);
    }
}
