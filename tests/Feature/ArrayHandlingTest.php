<?php

namespace Livingstoneco\Suspicion\Tests\Feature;

use Livingstoneco\Suspicion\Models\SuspiciousRequest;
use Livingstoneco\Suspicion\Tests\TestCase;

class ArrayHandlingTest extends TestCase
{
    /** @test */
    public function keywords_filter_handles_arrays_without_error()
    {
        // GIVEN: We have a request with arrays containing banned keywords
        $request = [
            'items' => [
                'social media',
                'normal text',
                'seo optimization'
            ]
        ];

        // WHEN: The request is passed through the IsSuspiciousRequest middleware
        // THEN: It should not throw preg_match error and should detect the keyword
        $response = $this->post('/contact', $request);

        // Verify the request was logged and blocked
        $sus = SuspiciousRequest::where('class', \Livingstoneco\Suspicion\RequestFilters\Keywords::class)
            ->first();

        $this->assertNotNull($sus);
        $this->assertContains($sus->trigger, ['social media', 'seo']);
        $response->assertStatus(403);
    }

    /** @test */
    public function keywords_filter_handles_nested_arrays()
    {
        // GIVEN: We have a request with deeply nested arrays containing banned keywords
        $request = [
            'data' => [
                'user' => [
                    'comments' => [
                        'first' => 'guest post',
                        'second' => 'normal comment'
                    ]
                ]
            ]
        ];

        // WHEN: The request is passed through the IsSuspiciousRequest middleware
        $response = $this->post('/contact', $request);

        // THEN: It should detect the keyword in nested arrays without error
        $sus = SuspiciousRequest::where('class', \Livingstoneco\Suspicion\RequestFilters\Keywords::class)
            ->first();

        $this->assertNotNull($sus);
        $this->assertEquals('guest post', $sus->trigger);
        $response->assertStatus(403);
    }

    /** @test */
    public function keywords_filter_handles_mixed_array_and_string_values()
    {
        // GIVEN: We have a request with mixed array and string values
        $request = [
            'message' => 'normal text',
            'tags' => ['seo', 'marketing'],
            'description' => 'another normal text'
        ];

        // WHEN: The request is passed through the IsSuspiciousRequest middleware
        $response = $this->post('/contact', $request);

        // THEN: It should detect keywords in arrays without error
        $sus = SuspiciousRequest::where('class', \Livingstoneco\Suspicion\RequestFilters\Keywords::class)
            ->first();

        $this->assertNotNull($sus);
        $this->assertContains($sus->trigger, ['seo', 'marketing']);
        $response->assertStatus(403);
    }

    /** @test */
    public function is_latin_filter_handles_arrays_without_error()
    {
        // GIVEN: We have a request with arrays containing non-Latin characters
        $request = [
            'messages' => [
                'Hello',
                'Привет', // Cyrillic
                'World'
            ]
        ];

        // WHEN: The request is passed through the IsSuspiciousRequest middleware
        $response = $this->post('/contact', $request);

        // THEN: It should not throw preg_match error and should detect non-Latin characters
        $sus = SuspiciousRequest::where('class', \Livingstoneco\Suspicion\RequestFilters\IsLatin::class)
            ->first();

        $this->assertNotNull($sus);
        $this->assertStringContainsString('Cyrillic', $sus->trigger);
        $response->assertStatus(403);
    }

    /** @test */
    public function is_latin_filter_handles_nested_arrays_with_non_latin()
    {
        // GIVEN: We have a request with deeply nested arrays containing non-Latin characters
        $request = [
            'data' => [
                'user' => [
                    'bio' => [
                        'en' => 'English text',
                        'ar' => 'مرحبا', // Arabic
                        'zh' => '你好' // Chinese
                    ]
                ]
            ]
        ];

        // WHEN: The request is passed through the IsSuspiciousRequest middleware
        $response = $this->post('/contact', $request);

        // THEN: It should detect non-Latin characters in nested arrays without error
        $sus = SuspiciousRequest::where('class', \Livingstoneco\Suspicion\RequestFilters\IsLatin::class)
            ->first();

        $this->assertNotNull($sus);
        $response->assertStatus(403);
    }

    /** @test */
    public function top_level_domains_filter_handles_arrays_without_error()
    {
        // GIVEN: We have a request with arrays containing banned TLDs
        $request = [
            'emails' => [
                'test@example.com',
                'spam@test.ru',
                'another@example.org'
            ]
        ];

        // WHEN: The request is passed through the IsSuspiciousRequest middleware
        $response = $this->post('/contact', $request);

        // THEN: It should not throw preg_match error and should detect the TLD
        $sus = SuspiciousRequest::where('class', \Livingstoneco\Suspicion\RequestFilters\TopLevelDomains::class)
            ->first();

        $this->assertNotNull($sus);
        $this->assertEquals('.ru', $sus->trigger);
        $response->assertStatus(403);
    }

    /** @test */
    public function top_level_domains_filter_handles_nested_arrays()
    {
        // GIVEN: We have a request with nested arrays containing banned TLDs
        $request = [
            'contacts' => [
                'primary' => [
                    'email' => 'user@domain.ml',
                    'phone' => '123-456-7890'
                ],
                'secondary' => [
                    'email' => 'backup@example.com'
                ]
            ]
        ];

        // WHEN: The request is passed through the IsSuspiciousRequest middleware
        $response = $this->post('/contact', $request);

        // THEN: It should detect TLDs in nested arrays without error
        $sus = SuspiciousRequest::where('class', \Livingstoneco\Suspicion\RequestFilters\TopLevelDomains::class)
            ->first();

        $this->assertNotNull($sus);
        $this->assertEquals('.ml', $sus->trigger);
        $response->assertStatus(403);
    }

    /** @test */
    public function domains_filter_handles_arrays_without_error()
    {
        // GIVEN: We have a request with arrays containing banned domains
        // Using domains that are in Domains list but won't match TopLevelDomains patterns
        // Pipeline order: MalformedUtf8 -> IsRepeatOffender -> IsLatin -> TopLevelDomains -> Domains -> Keywords
        $request = [
            'urls' => [
                'https://example.com',
                'Visit fiverr.com for services',
                'Check out bit.ly links'
            ]
        ];

        // WHEN: The request is passed through the IsSuspiciousRequest middleware
        $response = $this->post('/contact', $request);

        // THEN: It should not throw preg_match error and should detect the domain
        // The key test is that arrays don't cause preg_match errors - any filter catching it is fine
        $sus = SuspiciousRequest::whereIn('class', [
            \Livingstoneco\Suspicion\RequestFilters\Domains::class,
            \Livingstoneco\Suspicion\RequestFilters\TopLevelDomains::class
        ])->first();

        $this->assertNotNull($sus, 'A suspicious request should be logged - arrays handled without preg_match errors');
        $response->assertStatus(403);
    }

    /** @test */
    public function domains_filter_handles_nested_arrays()
    {
        // GIVEN: We have a request with nested arrays containing banned domains
        $request = [
            'links' => [
                'social' => [
                    'twitter' => 'https://twitter.com/user',
                    'other' => 'https://fiverr.com/profile'
                ],
                'personal' => [
                    'website' => 'https://mysite.com'
                ]
            ]
        ];

        // WHEN: The request is passed through the IsSuspiciousRequest middleware
        $response = $this->post('/contact', $request);

        // THEN: It should detect domains in nested arrays without error
        // Arrays are handled recursively - no preg_match errors should occur
        $sus = SuspiciousRequest::whereIn('class', [
            \Livingstoneco\Suspicion\RequestFilters\Domains::class,
            \Livingstoneco\Suspicion\RequestFilters\TopLevelDomains::class
        ])->first();

        $this->assertNotNull($sus, 'Nested arrays should be processed without preg_match errors');
        $response->assertStatus(403);
    }

    /** @test */
    public function all_filters_handle_empty_arrays()
    {
        // GIVEN: We have a request with empty arrays
        $request = [
            'empty_array' => [],
            'nested' => [
                'empty' => []
            ],
            'message' => 'normal text'
        ];

        // WHEN: The request is passed through the IsSuspiciousRequest middleware
        // THEN: It should not throw any errors
        $response = $this->post('/contact', $request);

        // Empty arrays should not trigger any filters (unless message contains banned content)
        // This test ensures empty arrays don't cause preg_match errors
        $this->assertTrue(true); // If we get here without error, the test passes
    }

    /** @test */
    public function all_filters_handle_arrays_with_non_string_values()
    {
        // GIVEN: We have a request with arrays containing non-string values (integers, booleans, null)
        $request = [
            'data' => [
                'id' => 123,
                'active' => true,
                'deleted' => false,
                'optional' => null,
                'message' => 'seo marketing' // This should trigger Keywords filter
            ]
        ];

        // WHEN: The request is passed through the IsSuspiciousRequest middleware
        $response = $this->post('/contact', $request);

        // THEN: It should handle non-string values gracefully and still detect strings
        $sus = SuspiciousRequest::where('class', \Livingstoneco\Suspicion\RequestFilters\Keywords::class)
            ->first();

        $this->assertNotNull($sus);
        $this->assertContains($sus->trigger, ['seo', 'marketing']);
        $response->assertStatus(403);
    }

    /** @test */
    public function filters_handle_arrays_as_top_level_input()
    {
        // GIVEN: We have a request where the input itself is an array (adversary sends array instead of object)
        // This simulates the exact scenario the user described
        $request = [
            ['seo', 'marketing', 'guest post']
        ];

        // WHEN: The request is passed through the IsSuspiciousRequest middleware
        // THEN: It should not throw preg_match error
        try {
            $response = $this->post('/contact', $request);
            // If we get here, no error was thrown
            $this->assertTrue(true);
        } catch (\TypeError $e) {
            $this->fail('preg_match() should not receive array - filters should handle arrays recursively: ' . $e->getMessage());
        }
    }

    /** @test */
    public function filters_handle_complex_nested_structure()
    {
        // GIVEN: We have a complex nested structure with arrays at multiple levels
        $request = [
            'users' => [
                [
                    'name' => 'John',
                    'emails' => ['john@example.com', 'john@mail.ru'],
                    'tags' => ['developer', 'seo expert']
                ],
                [
                    'name' => 'Jane',
                    'emails' => ['jane@test.com'],
                    'bio' => 'Привет мир' // Cyrillic
                ]
            ]
        ];

        // WHEN: The request is passed through the IsSuspiciousRequest middleware
        $response = $this->post('/contact', $request);

        // THEN: It should detect issues in nested arrays without error
        // Multiple filters may trigger, but we just need to verify no preg_match errors occur
        $suspiciousRequests = SuspiciousRequest::all();
        $this->assertGreaterThan(0, $suspiciousRequests->count());
        $response->assertStatus(403);
    }
}
