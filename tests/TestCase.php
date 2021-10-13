<?php

namespace Livingstoneco\Suspicion\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livingstoneco\Suspicion\SuspicionServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            SuspicionServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {

    }

}