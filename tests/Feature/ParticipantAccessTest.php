<?php

namespace Tests\Feature;

use Tests\TestCase;

class ParticipantAccessTest extends TestCase
{
    public function test_public_routes_accessible_without_auth()
    {
        $this->get('/')
             ->assertStatus(200);

        $this->get('/login')
             ->assertStatus(200);
    }

    public function test_non_authenticated_user_redirected_to_login_for_protected_routes()
    {
        $this->get('/my-profile')
             ->assertRedirect('/login');
    }

    public function test_debug_routes_accessible()
    {
        // Test that debug routes are accessible without authentication
        $this->get('/debug/participants/1')
             ->assertStatus(200);
    }
}


