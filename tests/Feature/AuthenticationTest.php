<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_public()
    {
        $this->get('/login')->assertStatus(200);
    }

    public function test_guest_cannot_open_movies()
    {
        $this->get('/movies')->assertRedirect('/login');
    }

    public function test_user_can_login_with_username()
    {
        User::create([
            'username' => 'aldmic',
            'name' => 'Aldmic',
            'email' => 'aldmic@example.com',
            'password' => bcrypt('123abc123'),
        ]);

        $this->post('/login', [
            'username' => 'aldmic',
            'password' => '123abc123',
        ])->assertRedirect('/movies');

        $this->assertAuthenticatedAs(User::where('username', 'aldmic')->first());
    }

    public function test_invalid_login_is_rejected()
    {
        User::create([
            'username' => 'aldmic',
            'name' => 'Aldmic',
            'email' => 'aldmic@example.com',
            'password' => bcrypt('123abc123'),
        ]);

        $this->from('/login')->post('/login', [
            'username' => 'aldmic',
            'password' => 'incorrect',
        ])->assertRedirect('/login')->assertSessionHasErrors('username');
    }
}
