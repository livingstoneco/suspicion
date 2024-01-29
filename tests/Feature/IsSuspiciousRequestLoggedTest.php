<?php
namespace Livingstoneco\Suspicion\Tests\Unit;

use Illuminate\Http\Request;
use Livingstoneco\Suspicion\Tests\TestCase;
use Livingstoneco\Suspicion\Models\SuspiciousRequest;
use Illuminate\Support\Facades\DB;

class IsSuspiciousRequestLoggedTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_logs_the_class_that_triggered_suspicion()
    {
        // GIVEN: We have a request that contains a suspicious term
        $request = ['message' => 'just some totally innocent text sex that shouldnt cryptocurrency trigger a block'];

        // WHEN: The request is passed through the IsSuspiciousRequest middleware
        $response = $this->post('/contact', $request);
        $response = $this->post('/contact', $request);
        $response = $this->post('/contact', $request);
        $response = $this->post('/contact', $request);
        $response = $this->post('/contact', $request);
        $response = $this->post('/contact', $request);
        $response = $this->post('/contact', $request);

        //THEN: The request is saved in the suspicious_requests table and includes the name of the class that triggered suspicion
        $sus = SuspiciousRequest::where('class', 'Livingstoneco\Suspicion\RequestFilters\Keywords')->firstOrFail();
        $this->assertEquals('Livingstoneco\Suspicion\RequestFilters\Keywords', $sus->class);
    }

    /** @test */
    public function it_logs_the_term_that_triggered_suspicion()
    {
        // GIVEN: We have a request that contains a suspicious term
        $request = ['message' => 'just some totally innocent text sex that shouldnt cryptocurrency trigger a block'];

        // WHEN: The request is passed through the IsSuspiciousRequest middleware
        $response = $this->post('/contact', $request);

        //THEN: The request is saved in the suspicious_requests table and includes the name of the class that triggered suspicion
        $sus = SuspiciousRequest::where('trigger', 'sex')->firstOrFail();
        $this->assertEquals('sex', $sus->trigger);
    }
}
