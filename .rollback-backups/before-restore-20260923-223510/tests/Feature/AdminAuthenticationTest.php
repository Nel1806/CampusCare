<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_administrator_can_sign_in(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@campuscare.test',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $this->withoutMiddleware();

        $this->post(route('admin.login.store'), [
            'email' => 'admin@campuscare.test',
            'password' => 'password',
        ])
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHasNoErrors();

        $this->assertAuthenticatedAs($admin);
    }
}
