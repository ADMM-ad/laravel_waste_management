<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\WasteCategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminWebTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(WasteCategorySeeder::class);
        $this->seed(AdminUserSeeder::class);
    }

    public function test_admin_can_login_to_web_portal(): void
    {
        $response = $this->post('/admin/login', [
            'email' => 'admin@banksampah.id',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticated();
    }

    public function test_citizen_user_cannot_access_admin_portal(): void
    {
        $response = $this->post('/admin/login', [
            'email' => 'warga@banksampah.id',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_authenticated_admin_can_view_dashboard(): void
    {
        $admin = User::where('email', 'admin@banksampah.id')->first();

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200)
            ->assertSee('Dashboard Operasional')
            ->assertSee('Total Laporan');
    }

    public function test_authenticated_admin_can_view_reports(): void
    {
        $admin = User::where('email', 'admin@banksampah.id')->first();

        $response = $this->actingAs($admin)->get('/admin/reports');
        $response->assertStatus(200)
            ->assertSee('Daftar Laporan Sampah');
    }

    public function test_authenticated_admin_can_view_users(): void
    {
        $admin = User::where('email', 'admin@banksampah.id')->first();

        $response = $this->actingAs($admin)->get('/admin/users');
        $response->assertStatus(200)
            ->assertSee('Pengguna Terdaftar');
    }
}
