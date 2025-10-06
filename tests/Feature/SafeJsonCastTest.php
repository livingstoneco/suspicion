<?php

namespace Livingstoneco\Suspicion\Tests\Feature;

use Livingstoneco\Suspicion\Models\SuspiciousRequest;
use Livingstoneco\Suspicion\Tests\TestCase;

class SafeJsonCastTest extends TestCase
{
    /** @test */
    public function it_saves_malformed_utf8_input_without_throwing_and_decodes_to_object()
    {
        $badText = "Hi Millershbc Com,\n\n\xF0\x28\x8C\x28 Check this wordpress �� link"; // include invalid sequences

        $created = SuspiciousRequest::create([
            'ip' => '127.0.0.1',
            'url' => '/contact',
            'input' => ['message' => $badText],
            'headers' => ['h' => $badText],
            'cookies' => ['c' => $badText],
            'class' => 'TestClass',
            'trigger' => 'test',
            'userAgent' => 'testing-agent',
        ]);

        $this->assertNotNull($created->id);

        $found = SuspiciousRequest::findOrFail($created->id);
        $this->assertIsObject($found->input);
        $this->assertIsObject($found->headers);
        $this->assertIsObject($found->cookies);
        $this->assertEquals('/contact', $found->url);
    }
}
