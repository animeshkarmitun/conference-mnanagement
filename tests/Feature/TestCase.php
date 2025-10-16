<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    use RefreshDatabase;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Only run RefreshDatabase if we're in a test environment
        if (app()->environment('testing')) {
            // Add any test-specific setup here
        }
    }
}
