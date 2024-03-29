<?php

namespace Tests\Feature\Api\Auth;

use Tests\TestCase;

class AuthApiTest extends TestCase
{
    public function testAuthenticationCategory()
    {
        $this->getJson('/api/categories')
            ->assertStatus(401);

        $this->getJson('/api/categories/fake_id')
            ->assertStatus(401);

        $this->postJson('/api/categories')
            ->assertStatus(401);

        $this->putJson('/api/categories/fake_id')
            ->assertStatus(401);

        $this->deleteJson('/api/categories/fake_id')
            ->assertStatus(401);
    }

    public function testAuthenticationGenres()
    {
        $this->getJson('/api/genres')
            ->assertStatus(401);

        $this->getJson('/api/genres/fake_id')
            ->assertStatus(401);

        $this->postJson('/api/genres')
            ->assertStatus(401);

        $this->putJson('/api/genres/fake_id')
            ->assertStatus(401);

        $this->deleteJson('/api/genres/fake_id')
            ->assertStatus(401);
    }

    public function testAuthenticationCastMembers()
    {
        $this->getJson('/api/cast_members')
            ->assertStatus(401);

        $this->getJson('/api/cast_members/fake_id')
            ->assertStatus(401);

        $this->postJson('/api/cast_members')
            ->assertStatus(401);

        $this->putJson('/api/cast_members/fake_id')
            ->assertStatus(401);

        $this->deleteJson('/api/cast_members/fake_id')
            ->assertStatus(401);
    }

    public function testAuthenticationVideo()
    {
        $this->getJson('/api/videos')
            ->assertStatus(401);

        $this->getJson('/api/videos/fake_id')
            ->assertStatus(401);

        $this->postJson('/api/videos')
            ->assertStatus(401);

        $this->putJson('/api/videos/fake_id')
            ->assertStatus(401);

        $this->deleteJson('/api/videos/fake_id')
            ->assertStatus(401);
    }
}
