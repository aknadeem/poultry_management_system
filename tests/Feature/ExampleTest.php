<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example that validates Laravel boots correctly
     */
    public function test_example()
    {
        $response = $this->get('/');

        // Route may redirect (302) or return 200, both are acceptable
        $this->assertTrue(
            in_array($response->status(), [200, 302]),
            "Expected status [200, 302] but got {$response->status()}"
        );
    }
}
