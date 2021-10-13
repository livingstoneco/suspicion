<?php

namespace Livingstoneco\Suspicion\Tests\Unit;

use Illuminate\Http\Request;
use Livingstoneco\Suspicion\Tests\TestCase;
use Livingstoneco\Suspicion\Models\SuspiciousRequest;
use Livingstoneco\Suspicion\Http\Middleware\IsRequestSuspicious;

class IsSuspiciousRequestMiddlewareTest extends TestCase
{

    /** @test */
    function it_throws_an_exceptions_when_request_contains_banned_keywords()
    {
        $request = Request::create('/contact', 'POST')->merge(['message' => 'social media marketing']);
        
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        
        (new IsRequestSuspicious())->handle($request, function ($request) {});

    }

    /** @test */
    function it_throws_an_exceptions_when_request_contains_banned_domain()
    {
        $request = Request::create('/contact', 'POST')->merge(['email' => 'mail.ru']);
        
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        (new IsRequestSuspicious())->handle($request, function ($request) {});
    }

    /** @test */
    function it_throws_an_exceptions_when_request_contains_banned_top_level_domain()
    {
        $request = Request::create('/contact', 'POST')->merge(['email' => '.ru']);
        
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        (new IsRequestSuspicious())->handle($request, function ($request) {});
    }
    
    /** @test */
    function it_throws_an_exceptions_when_request_contains_cyrillic_characters()
    {
        $request = Request::create('/contact', 'POST')->merge(['message' => 'я тот, кто стучит']);
        
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        (new IsRequestSuspicious())->handle($request, function ($request) {});
    }

    
}