<?php

namespace Tests\Contracts;

use Tests\TestCase;

class ApiContractTest extends TestCase
{
    public function test_api_user_requires_authentication(): void
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401)->assertJsonStructure(['message']);
    }
}
