<?php

namespace Livingstoneco\Suspicion\Tests\Unit;

use Illuminate\Http\Request;
use Livingstoneco\Suspicion\Tests\TestCase;
use Livingstoneco\Suspicion\Models\SuspiciousRequest;
use Livingstoneco\Suspicion\Http\Middleware\IsRequestSuspicious;

class IsSuspiciousRequesteTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        
    }

   /** @test */
   function it_logs_request_when_contains_banned_keywords()
   {
        // GIVEN: We have a request that contains banned keyword(s)
        $request = Request::create('/contact', 'POST')->merge(['message' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore social media marketing. ullamco laboris nisi ut aliquip ex ea commodo consequat.']);
        $middleware = new IsRequestSuspicious();

        try {
            // WHEN: The request is passed through the IsSuspiciousRequest middleware
            $middleware->handle($request, function ($request) {});
        } finally {
            //THEN: The request is saved in the suspicious_requests table
            $sus = SuspiciousRequest::where('input->message', 'like', '%social media marketing%')->firstOrFail();
            $this->assertEquals(1, $sus->count());
        }
   }

   /** @test */
   function it_logs_request_when_contains_banned_domain()
   {
        // GIVEN: We have a request that contains a banned domain    
        $request = Request::create('/contact', 'POST')->merge(['email' => 'fake@mail.ru']);
        $middleware = new IsRequestSuspicious();

        try {
            // WHEN: The request is passed through the IsSuspiciousRequest middleware
            $middleware->handle($request, function ($request) {});
        } finally {
            //THEN: The request is saved in the suspicious_requests table
            $sus = SuspiciousRequest::where('input->email', 'like', '%mail.ru%')->get();
            $this->assertEquals(1, $sus->count());
        }
   }

   /** @test */
   function it_logs_request_when_contains_banned_top_level_domain()
   {
        // GIVEN: We have a request that contains a banned top level domain    
        $request = Request::create('/contact', 'POST')->merge(['email' => 'fake@test.tst']);
        $middleware = new IsRequestSuspicious();

        try {
            // WHEN: The request is passed through the IsSuspiciousRequest middleware
            $middleware->handle($request, function ($request) {});
        } finally {
            //THEN: The request is saved in the suspicious_requests table
            $sus = SuspiciousRequest::where('input->email', 'like', '%test.tst%')->get();
            $this->assertEquals(1, $sus->count());
        }
   }

    /** @test */
    function it_logs_request_when_contains_cyrillic_chars()
    {
         // GIVEN: We have a request that contains cyrillic chars
         $request = Request::create('/contact', 'POST')->merge(['message' => 'я тот, кто стучит']);
         $middleware = new IsRequestSuspicious();
 
         try {
             // WHEN: The request is passed through the IsSuspiciousRequest middleware
             $middleware->handle($request, function ($request) {});
         } finally {
             //THEN: The request is saved in the suspicious_requests table
             $sus = SuspiciousRequest::where('input->message', 'like', '%кто%')->get();
             $this->assertEquals(1, $sus->count());
         }
    }
}