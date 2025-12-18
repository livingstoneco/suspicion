<?php

namespace Livingstoneco\Suspicion\Tests\Unit;

use Livingstoneco\Suspicion\Models\SuspiciousRequest;
use Livingstoneco\Suspicion\Tests\TestCase;

class MalformedUtf8Test extends TestCase
{
    /** @test */
    public function it_logs_request_when_contains_malformed_utf8_in_input()
    {
        // GIVEN: We have a request that contains malformed UTF-8 in input parameters
        $malformedString = "\xFF\xFE\x00\x01"; // Invalid UTF-8 sequence
        $request = ['message' => $malformedString];

        // WHEN: The request is passed through the IsSuspiciousRequest middleware
        $response = $this->post('/contact', $request);

        // THEN: The request is saved in the suspicious_requests table and a 422 status code is returned
        $sus = SuspiciousRequest::where('class', \Livingstoneco\Suspicion\RequestFilters\MalformedUtf8::class)
            ->where('trigger', 'Malformed UTF-8 detected')
            ->firstOrFail();

        $this->assertEquals(1, SuspiciousRequest::where('class', \Livingstoneco\Suspicion\RequestFilters\MalformedUtf8::class)->count());
        $this->assertNotNull($sus->input);
        $response->assertStatus(422);
    }

    /** @test */
    public function it_logs_request_when_contains_malformed_utf8_in_nested_array()
    {
        // GIVEN: We have a request that contains malformed UTF-8 in nested array
        $malformedString = "\xE0\x80\x80"; // Invalid UTF-8 sequence (overlong encoding)
        $request = [
            'user' => [
                'name' => $malformedString,
                'email' => 'test@example.com'
            ]
        ];

        // WHEN: The request is passed through the IsSuspiciousRequest middleware
        $response = $this->post('/contact', $request);

        // THEN: The request is saved in the suspicious_requests table and a 422 status code is returned
        $sus = SuspiciousRequest::where('class', \Livingstoneco\Suspicion\RequestFilters\MalformedUtf8::class)
            ->where('trigger', 'Malformed UTF-8 detected')
            ->firstOrFail();

        $this->assertEquals(1, SuspiciousRequest::where('class', \Livingstoneco\Suspicion\RequestFilters\MalformedUtf8::class)->count());
        $response->assertStatus(422);
    }

    /** @test */
    public function it_logs_request_when_contains_malformed_utf8_in_headers()
    {
        // GIVEN: We have a request that contains malformed UTF-8 in headers
        $malformedString = "\xC0\xAF"; // Invalid UTF-8 sequence
        $request = ['message' => 'hello'];

        // WHEN: The request is passed through the IsSuspiciousRequest middleware with malformed header
        $response = $this->withHeaders([
            'X-Custom-Header' => $malformedString
        ])->post('/contact', $request);

        // THEN: The request is saved in the suspicious_requests table and a 422 status code is returned
        $sus = SuspiciousRequest::where('class', \Livingstoneco\Suspicion\RequestFilters\MalformedUtf8::class)
            ->where('trigger', 'Malformed UTF-8 detected')
            ->firstOrFail();

        $this->assertEquals(1, SuspiciousRequest::where('class', \Livingstoneco\Suspicion\RequestFilters\MalformedUtf8::class)->count());
        $response->assertStatus(422);
    }

    /** @test */
    public function it_checks_cookies_for_malformed_utf8()
    {
        // GIVEN: We have a request with cookies containing valid UTF-8
        // Note: Laravel sanitizes cookies during parsing, so malformed UTF-8 in cookies
        // is difficult to test in the test environment. This test verifies the filter
        // processes cookies without errors when they are present.
        $request = ['message' => 'test message'];

        // WHEN: The request is passed through the IsSuspiciousRequest middleware with cookies
        $response = $this->withCookie('test_cookie', 'valid_cookie_value')
            ->post('/contact', $request);

        // THEN: The MalformedUtf8 filter should not trigger (no malformed UTF-8 detected)
        // This test ensures the cookie checking logic doesn't cause errors
        $malformedUtf8Count = SuspiciousRequest::where('class', \Livingstoneco\Suspicion\RequestFilters\MalformedUtf8::class)->count();
        $this->assertEquals(0, $malformedUtf8Count);

        // Note: The request may be blocked by other filters, but not by MalformedUtf8
    }

    /** @test */
    public function it_allows_valid_utf8_to_pass()
    {
        // GIVEN: We have a request that contains valid UTF-8
        $request = ['message' => 'Hello, 世界! 🌍'];

        // WHEN: The request is passed through the IsSuspiciousRequest middleware
        $response = $this->post('/contact', $request);

        // THEN: The request should not be blocked by MalformedUtf8 filter
        // (it may be blocked by other filters, but not this one)
        $malformedUtf8Count = SuspiciousRequest::where('class', \Livingstoneco\Suspicion\RequestFilters\MalformedUtf8::class)->count();
        $this->assertEquals(0, $malformedUtf8Count);
    }

    /** @test */
    public function it_sanitizes_malformed_utf8_before_saving_to_database()
    {
        // GIVEN: We have a request that contains malformed UTF-8
        $malformedString = "\xFF\xFE\x00\x01"; // Invalid UTF-8 sequence
        $request = ['message' => $malformedString];

        // WHEN: The request is passed through the IsSuspiciousRequest middleware
        $response = $this->post('/contact', $request);

        // THEN: The request is saved with sanitized data (no database errors)
        $sus = SuspiciousRequest::where('class', \Livingstoneco\Suspicion\RequestFilters\MalformedUtf8::class)
            ->where('trigger', 'Malformed UTF-8 detected')
            ->firstOrFail();

        // Verify the data was sanitized and can be retrieved without errors
        // Note: SafeJson cast returns objects, not arrays
        $this->assertNotNull($sus->input);
        $this->assertIsObject($sus->input);
        $this->assertObjectHasProperty('message', $sus->input);

        // The sanitized message should be a valid UTF-8 string (malformed bytes removed)
        $sanitizedMessage = $sus->input->message;
        $this->assertIsString($sanitizedMessage);
        $this->assertTrue(mb_check_encoding($sanitizedMessage, 'UTF-8'));

        $response->assertStatus(422);
    }

    /** @test */
    public function it_sanitizes_malformed_utf8_in_array_keys()
    {
        // GIVEN: We have a request that contains malformed UTF-8 in array values
        // Note: PHP arrays cannot have malformed UTF-8 keys, so we test that malformed values in nested structures are sanitized
        $malformedValue = "value_with\xFF\xFE"; // Invalid UTF-8 sequence in value
        $request = ['normal_key' => $malformedValue];

        // WHEN: The request is passed through the IsSuspiciousRequest middleware
        $response = $this->post('/contact', $request);

        // THEN: The request is saved with sanitized data (no database errors)
        $sus = SuspiciousRequest::where('class', \Livingstoneco\Suspicion\RequestFilters\MalformedUtf8::class)
            ->where('trigger', 'Malformed UTF-8 detected')
            ->firstOrFail();

        // Verify the data was sanitized and can be retrieved without errors
        // Note: SafeJson cast returns objects, not arrays
        $this->assertNotNull($sus->input);
        $this->assertIsObject($sus->input);
        $this->assertObjectHasProperty('normal_key', $sus->input);

        // The sanitized value should be valid UTF-8
        $sanitizedValue = $sus->input->normal_key;
        $this->assertIsString($sanitizedValue);
        $this->assertTrue(mb_check_encoding($sanitizedValue, 'UTF-8'));

        $response->assertStatus(422);
    }

    /** @test */
    public function it_handles_multiple_malformed_utf8_sequences()
    {
        // GIVEN: We have a request that contains multiple malformed UTF-8 sequences
        $request = [
            'field1' => "\xFF\xFE",
            'field2' => "\xC0\xAF",
            'field3' => "\xE0\x80\x80",
        ];

        // WHEN: The request is passed through the IsSuspiciousRequest middleware
        $response = $this->post('/contact', $request);

        // THEN: The request is saved in the suspicious_requests table and a 422 status code is returned
        $sus = SuspiciousRequest::where('class', \Livingstoneco\Suspicion\RequestFilters\MalformedUtf8::class)
            ->where('trigger', 'Malformed UTF-8 detected')
            ->firstOrFail();

        $this->assertEquals(1, SuspiciousRequest::where('class', \Livingstoneco\Suspicion\RequestFilters\MalformedUtf8::class)->count());

        // Verify all fields were sanitized (SafeJson returns objects)
        $this->assertTrue(mb_check_encoding($sus->input->field1, 'UTF-8'));
        $this->assertTrue(mb_check_encoding($sus->input->field2, 'UTF-8'));
        $this->assertTrue(mb_check_encoding($sus->input->field3, 'UTF-8'));

        $response->assertStatus(422);
    }
}
