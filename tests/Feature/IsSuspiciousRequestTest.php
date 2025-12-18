<?php
namespace Livingstoneco\Suspicion\Tests\Unit;

use Illuminate\Http\Request;
use Livingstoneco\Suspicion\Tests\TestCase;
use Livingstoneco\Suspicion\Models\SuspiciousRequest;

class IsSuspiciousRequestTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_logs_request_when_contains_banned_keywords()
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
    public function it_logs_request_when_contains_banned_domain()
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
    public function it_logs_request_when_contains_banned_top_level_domain()
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
    public function it_logs_request_when_contains_cyrillic_chars()
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

    /** @test */
    public function it_handles_malformed_user_agent_strings()
    {
        // GIVEN: We have a request with a malformed user agent string (invalid UTF-8 sequence)
        $malformedUserAgent = "\xFF\xFE\x00\x01"; // Invalid UTF-8 sequence
        $request = ['message' => 'social media marketing'];

        // WHEN: The request is passed through the IsSuspiciousRequest middleware with malformed user agent
        $response = $this->withHeaders([
            'User-Agent' => $malformedUserAgent
        ])->post('/contact', $request);

        //THEN: The request is saved in the suspicious_requests table and the user agent is handled gracefully
        $sus = SuspiciousRequest::where('input->message', 'like', '%social media marketing%')->firstOrFail();
        $this->assertNotNull($sus->userAgent);
        $response->assertStatus(422);
    }

    /** @test */
    public function it_handles_user_agent_with_null_bytes()
    {
        // GIVEN: We have a request with a user agent containing null bytes
        $userAgentWithNulls = "Mozilla/5.0\x00\x00 (Windows NT 10.0; Win64; x64)";
        $request = ['message' => 'social media marketing'];

        // WHEN: The request is passed through the IsSuspiciousRequest middleware
        $response = $this->withHeaders([
            'User-Agent' => $userAgentWithNulls
        ])->post('/contact', $request);

        //THEN: The request is saved in the suspicious_requests table and the user agent is handled gracefully
        $sus = SuspiciousRequest::where('input->message', 'like', '%social media marketing%')->firstOrFail();
        $this->assertNotNull($sus->userAgent);
        $response->assertStatus(422);
    }

    /** @test */
    public function it_handles_user_agent_with_encoding_issues()
    {
        // GIVEN: We have a request with a user agent that has encoding issues (like the example provided)
        // This simulates the scenario where mb_convert_encoding is used to handle encoding
        $userAgent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36";
        $request = ['message' => 'social media marketing'];

        // WHEN: The request is passed through the IsSuspiciousRequest middleware
        // The middleware should handle encoding conversion gracefully
        $response = $this->withHeaders([
            'User-Agent' => $userAgent
        ])->post('/contact', $request);

        //THEN: The request is saved in the suspicious_requests table with properly encoded user agent
        $sus = SuspiciousRequest::where('input->message', 'like', '%social media marketing%')->firstOrFail();
        $this->assertNotNull($sus->userAgent);
        $this->assertIsString($sus->userAgent);
        // Verify the user agent was stored correctly (after mb_convert_encoding processing)
        $this->assertStringContainsString('Mozilla', $sus->userAgent);
        $response->assertStatus(422);
    }

    /** @test */
    public function it_handles_extremely_long_user_agent_strings()
    {
        // GIVEN: We have a request with an extremely long user agent string
        $longUserAgent = str_repeat('A', 10000); // 10KB user agent
        $request = ['message' => 'social media marketing'];

        // WHEN: The request is passed through the IsSuspiciousRequest middleware
        $response = $this->withHeaders([
            'User-Agent' => $longUserAgent
        ])->post('/contact', $request);

        //THEN: The request is saved in the suspicious_requests table and the user agent is handled gracefully
        $sus = SuspiciousRequest::where('input->message', 'like', '%social media marketing%')->firstOrFail();
        $this->assertNotNull($sus->userAgent);
        $this->assertIsString($sus->userAgent);
        $response->assertStatus(422);
    }

    /** @test */
    public function it_handles_empty_user_agent_string()
    {
        // GIVEN: We have a request with an empty user agent string
        $request = ['message' => 'social media marketing'];

        // WHEN: The request is passed through the IsSuspiciousRequest middleware with empty user agent
        $response = $this->withHeaders([
            'User-Agent' => ''
        ])->post('/contact', $request);

        //THEN: The request is saved in the suspicious_requests table and empty user agent is handled
        $sus = SuspiciousRequest::where('input->message', 'like', '%social media marketing%')->firstOrFail();
        $this->assertNotNull($sus->userAgent);
        $response->assertStatus(422);
    }
}
