<?php

namespace Livingstoneco\Suspicion\Tests\Unit;

use Illuminate\Http\Request;
use Livingstoneco\Suspicion\Tests\TestCase;

class IsSuspiciousRequestMiddlewareTest extends TestCase
{

    /** @test */
    function it_throws_an_exceptions_when_request_contains_banned_keywords()
    {
        $response = $this->post('/contact', ['message' => 'social media marketing']);

        $response->assertStatus(422);
    }

    /** @test */
    function it_throws_an_exceptions_when_request_contains_banned_domain()
    {
        $response = $this->post('/contact', ['email' => 'mail.ru']);

        $response->assertStatus(422);
    }

    /** @test */
    function it_throws_an_exceptions_when_request_contains_banned_top_level_domain()
    {
        $response = $this->post('/contact', ['email' => '.ru']);

        $response->assertStatus(422);
    }
    
    /** @test */
    function it_throws_an_exceptions_when_request_contains_cyrillic_characters()
    {
        $response = $this->post('/contact', ['message' => 'я тот, кто стучит']);

        $response->assertStatus(422);
    }
}