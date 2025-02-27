<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BasicTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test basic application response.
     *
     * @return void
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    /**
     * Test database connection.
     *
     * @return void
     */
    public function test_database_connection(): void
    {
        $this->assertDatabaseCount('users', 0);
    }
}