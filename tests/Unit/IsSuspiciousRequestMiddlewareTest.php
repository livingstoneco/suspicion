<?php
namespace Livingstoneco\Suspicion\Tests\Unit;

use Livingstoneco\Suspicion\Tests\TestCase;

class IsSuspiciousRequestMiddlewareTest extends TestCase
{
    /** @test */
    public function it_throws_an_exceptions_when_request_contains_banned_keywords()
    {
        $response = $this->post('/contact', ['message' => 'social media marketing']);

        $response->assertSee(config('suspicion.error_message'));
        $response->assertStatus(422);
    }

    /** @test */
    public function it_throws_an_exceptions_when_request_contains_banned_domain()
    {
        $response = $this->post('/contact', ['email' => 'mail.ru']);

        $response->assertSee(config('suspicion.error_message'));
        $response->assertStatus(422);
    }

    /** @test */
    public function it_throws_an_exceptions_when_request_contains_banned_top_level_domain()
    {
        $response = $this->post('/contact', ['email' => '.ru']);

        $response->assertStatus(422);
    }

    /** @test */
    public function it_throws_an_exceptions_when_request_contains_cyrillic_characters()
    {
        $response = $this->post('/contact', ['message' => 'я тот, кто стучит']);

        $response->assertSee(config('suspicion.error_message'));
        $response->assertStatus(422);
    }

    /** @test */
    public function it_throws_an_exceptions_when_request_contains_georgian_characters()
    {
        $response = $this->post('/contact', ['message' => 'რამდენი ფულის გაკეთება შეგიძლიათ']);

        $response->assertSee(config('suspicion.error_message'));
        $response->assertStatus(422);
    }

    /** @test */
    public function it_throws_an_exceptions_when_request_contains_arabic_characters()
    {
        $response = $this->post('/contact', ['message' => 'مرحبا']);

        $response->assertSee(config('suspicion.error_message'));
        $response->assertStatus(422);
    }

    /** @test */
    public function it_throws_an_exceptions_when_request_contains_chinese_characters()
    {
        $response = $this->post('/contact', ['message' => '你好']);

        $response->assertStatus(422);
    }
}
