<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_citizen_can_register_successfully(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Ahmad Warga',
            'email' => 'ahmad@gmail.com',
            'password' => 'password123',
            'phone_number' => '08123456789',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Registrasi akun berhasil',
            ])
            ->assertJsonStructure([
                'data' => [
                    'user' => ['id', 'name', 'email', 'phone_number', 'role'],
                    'token',
                ],
            ]);

        $this->assertDatabaseHas('users', ['email' => 'ahmad@gmail.com']);
    }

    public function test_citizen_can_login_with_valid_credentials(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'testuser@gmail.com',
            'password' => Hash::make('secret123'),
            'is_active' => true,
        ]);
        $user->assignRole('user');

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'testuser@gmail.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure(['data' => ['token', 'user']]);
    }

    public function test_inactive_user_is_forbidden_from_logging_in(): void
    {
        $user = User::create([
            'name' => 'Banned User',
            'email' => 'banned@gmail.com',
            'password' => Hash::make('secret123'),
            'is_active' => false,
        ]);
        $user->assignRole('user');

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'banned@gmail.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(403)
            ->assertJson(['success' => false]);
    }

    public function test_authenticated_user_can_get_profile(): void
    {
        $user = User::create([
            'name' => 'Active Profile',
            'email' => 'profile@gmail.com',
            'password' => Hash::make('secret123'),
            'is_active' => true,
        ]);
        $user->assignRole('user');

        $token = $user->createToken('test_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/auth/me');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'email' => 'profile@gmail.com',
                ],
            ]);
    }
}
