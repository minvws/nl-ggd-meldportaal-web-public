<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use Tests\Feature\TestCase;

class OpenApiTest extends TestCase
{
    public function testOpenApiEndpoint(): void
    {
        $response = $this->get('/openapi');
        $response
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJsonPath('paths./api/v2/tests.post.summary', 'Create new test result.')
            ->assertJsonPath('paths./api/health.get.summary', 'Health Endpoint');
    }
}
