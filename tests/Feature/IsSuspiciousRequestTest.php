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
    }

   /** @test */
   function it_logs_request_when_contains_banned_keywords()
   {
        // GIVEN: We have a request that contains banned keyword(s)
        $request = ['message' => 'social media marketing'];

        // WHEN: The request is passed through the IsSuspiciousRequest middleware
        $response = $this->post('/contact', $request);

        //THEN: The request is saved in the suspicious_requests table and a 422 status code is returned
        $sus = SuspiciousRequest::where('input->message', 'like', '%social media marketing%')->firstOrFail();
        $this->assertEquals(1, $sus->count());
        $response->assertStatus(422);
   }

   /** @test */
   function it_logs_request_when_contains_banned_domain()
   {
       // GIVEN: We have a request that contains banned domain
       $request = ['email' => 'fake@mail.ru'];

       // WHEN: The request is passed through the IsSuspiciousRequest middleware
       $response = $this->post('/contact', $request);

        //THEN: The request is saved in the suspicious_requests table and a 422 status code is returned
        $sus = SuspiciousRequest::where('input->email', 'like', '%mail.ru%')->get();
        $this->assertEquals(1, $sus->count());
        $response->assertStatus(422);
   }

   /** @test */
   function it_logs_request_when_contains_banned_top_level_domain()
   {
       // GIVEN: We have a request that contains banned top level domain
       $request = ['email' => 'fake@test.tst'];

       // WHEN: The request is passed through the IsSuspiciousRequest middleware
       $response = $this->post('/contact', $request);

        //THEN: The request is saved in the suspicious_requests table and a 422 status code is returned
        $sus = SuspiciousRequest::where('input->email', 'like', '%test.tst%')->get();
        $this->assertEquals(1, $sus->count());
        $response->assertStatus(422);
   }

    /** @test */
    function it_logs_request_when_contains_cyrillic_chars()
    {
        // GIVEN: We have a request that contains cyrillic characters
       $request = ['message' => 'я тот, кто стучит'];

       // WHEN: The request is passed through the IsSuspiciousRequest middleware
       $response = $this->post('/contact', $request);

        //THEN: The request is saved in the suspicious_requests table and a 422 status code is returned
        $sus = SuspiciousRequest::where('input->message', 'like', '%кто%')->get();
        $this->assertEquals(1, $sus->count());
        $response->assertStatus(422);
    }
}