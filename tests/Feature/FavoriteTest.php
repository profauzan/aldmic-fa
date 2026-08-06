<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_save_and_remove_a_favorite()
    {
        $user = User::create([
            'username' => 'aldmic',
            'name' => 'Aldmic',
            'email' => 'aldmic@example.com',
            'password' => bcrypt('123abc123'),
        ]);

        $this->actingAs($user)->postJson('/favorites', [
            'imdb_id' => 'tt15239678',
            'title' => 'Dune: Part Two',
            'year' => '2024',
            'type' => 'movie',
            'poster' => 'https://example.com/dune.jpg',
        ])->assertStatus(201)->assertJson(['favorite' => true]);

        $this->assertDatabaseHas('favorites', ['user_id' => $user->id, 'imdb_id' => 'tt15239678']);

        $this->actingAs($user)->deleteJson('/favorites/tt15239678')
            ->assertOk()->assertJson(['deleted' => true]);

        $this->assertDatabaseMissing('favorites', ['imdb_id' => 'tt15239678']);
    }

    public function test_guest_cannot_save_a_favorite()
    {
        $this->postJson('/favorites', [
            'imdb_id' => 'tt15239678',
            'title' => 'Dune: Part Two',
        ])->assertStatus(401);
    }
}
