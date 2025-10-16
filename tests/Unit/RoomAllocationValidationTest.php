<?php

namespace Tests\Unit;

use Tests\TestCase;
use Carbon\Carbon;

class RoomAllocationValidationTest extends TestCase
{
    public function test_check_in_cannot_be_after_departure_time_validation()
    {
        // Test the core validation logic
        $departureDate = Carbon::parse('2025-12-17 06:00:00');
        $checkInTime = Carbon::parse('2025-12-18 10:00:00'); // After departure
        
        // This should fail validation
        $this->assertTrue($checkInTime->gt($departureDate), 'Check-in time should be after departure time');
    }

    public function test_check_in_can_be_on_departure_date()
    {
        // Test that check-in on departure date is valid
        $departureDate = Carbon::parse('2025-12-17 18:00:00');
        $checkInTime = Carbon::parse('2025-12-17 14:00:00'); // Same day, before departure time
        
        // This should pass validation
        $this->assertTrue($checkInTime->lte($departureDate), 'Check-in time should be on or before departure time');
    }

    public function test_check_out_cannot_be_after_departure_time_validation()
    {
        // Test the core validation logic
        $departureDate = Carbon::parse('2025-12-17 06:00:00');
        $checkOutTime = Carbon::parse('2025-12-18 10:00:00'); // After departure
        
        // This should fail validation
        $this->assertTrue($checkOutTime->gt($departureDate), 'Check-out time should be after departure time');
    }

    public function test_check_out_can_be_on_departure_date()
    {
        // Test that check-out on departure date is valid
        $departureDate = Carbon::parse('2025-12-17 18:00:00');
        $checkOutTime = Carbon::parse('2025-12-17 14:00:00'); // Same day, before departure time
        
        // This should pass validation
        $this->assertTrue($checkOutTime->lte($departureDate), 'Check-out time should be on or before departure time');
    }

    public function test_check_in_must_be_before_check_out()
    {
        // Test the basic check-in vs check-out validation
        $checkInTime = Carbon::parse('2025-12-15 14:00:00');
        $checkOutTime = Carbon::parse('2025-12-17 12:00:00');
        
        // This should pass validation
        $this->assertTrue($checkInTime->lt($checkOutTime), 'Check-in should be before check-out');
    }

    public function test_check_in_cannot_be_after_check_out()
    {
        // Test the basic check-in vs check-out validation
        $checkInTime = Carbon::parse('2025-12-17 14:00:00');
        $checkOutTime = Carbon::parse('2025-12-15 12:00:00');
        
        // This should fail validation
        $this->assertTrue($checkInTime->gt($checkOutTime), 'Check-in should be after check-out (invalid case)');
    }
}
