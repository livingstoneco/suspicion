<?php
namespace Livingstoneco\Suspicion\Tests\Unit;

use Livingstoneco\Suspicion\Tests\TestCase;

class IsRepeatOffenderMiddlewareTest extends TestCase
{
    /** @test */
    public function it_blocks_requests_from_users_who_have_been_flagged_multiple_times()
    {
        // GIVEN: User has had multiple requests flagged as suspicious
        for ($i = 0; $i <= 5; $i++) {
            $this->post('/contact', ['message' => 'social media marketing']);
        }

        // WHEN: User tries to access any page - regardless of HTTP method - on the website
        $request = $this->get('/');

        // THEN: Request is blocked and error page is shown
        $request->assertSee(config('suspicion.repeat_offenders.message'));
        $request->assertStatus(config('suspicion.repeat_offenders.http_code'));
    }
}
