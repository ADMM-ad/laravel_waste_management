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
            'email' => 'admin',
            'password' => '12345678',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticated();
    }

    public function test_user_with_wrong_password_cannot_access_portal(): void
    {
        $response = $this->post('/admin/login', [
            'email' => 'admin',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_authenticated_admin_can_view_dashboard(): void
    {
        $admin = User::where('email', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200)
            ->assertSee('Dashboard Operasional')
            ->assertSee('Total Laporan');
    }

    public function test_authenticated_admin_can_view_reports(): void
    {
        $admin = User::where('email', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin/reports');
        $response->assertStatus(200)
            ->assertSee('Daftar Laporan Sampah');
    }

    public function test_authenticated_admin_can_view_users(): void
    {
        $admin = User::where('email', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin/users');
        $response->assertStatus(200)
            ->assertSee('Pengguna Terdaftar');
    }

    public function test_citizen_can_register_via_web_and_auto_login(): void
    {
        $showResponse = $this->get('/admin/register');
        $showResponse->assertStatus(200)
            ->assertSee('Pendaftaran Akun Warga');

        $registerResponse = $this->post('/admin/register', [
            'name' => 'Warga Baru Web',
            'email' => 'wargabaru',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'phone_number' => '081234567890',
        ]);

        $registerResponse->assertRedirect('/admin/dashboard');
        $this->assertAuthenticated();

        $user = User::where('email', 'wargabaru')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('user'));
        $this->assertFalse($user->hasRole('admin'));
    }

    public function test_admin_cannot_toggle_or_delete_fellow_admin(): void
    {
        $admin1 = User::where('email', 'admin')->first();

        $admin2 = User::create([
            'name' => 'Admin Dua',
            'email' => 'admin2',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);
        $admin2->assignRole('admin');

        // Attempt to toggle fellow admin status
        $toggleResponse = $this->actingAs($admin1)->post("/admin/users/{$admin2->id}/toggle-status");
        $toggleResponse->assertSessionHas('error');
        $this->assertTrue($admin2->fresh()->is_active);

        // Attempt to delete fellow admin
        $deleteResponse = $this->actingAs($admin1)->delete("/admin/users/{$admin2->id}");
        $deleteResponse->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin2->id]);
    }

    public function test_dashboard_displays_urgency_and_no_category_distribution(): void
    {
        $admin = User::where('email', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200)
            ->assertSee('Sebaran Urgensi Laporan')
            ->assertSee('Estimasi Volume Sampah')
            ->assertSee('Aktivitas Penanganan Sampah Terbaru')
            ->assertDontSee('Distribusi Jenis Sampah');
    }

    public function test_citizen_cannot_access_user_management_or_ai_evaluation(): void
    {
        $citizen = User::create([
            'name' => 'Warga Test',
            'email' => 'warga_test',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);
        $citizen->assignRole('user');

        // Access users
        $resUsers = $this->actingAs($citizen)->get('/admin/users');
        $resUsers->assertStatus(403);

        // Access ai-evaluation
        $resAi = $this->actingAs($citizen)->get('/admin/ai-evaluation');
        $resAi->assertStatus(403);
    }

    public function test_citizen_cannot_mutate_report_status(): void
    {
        $citizen = User::create([
            'name' => 'Warga Test',
            'email' => 'warga_test',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);
        $citizen->assignRole('user');

        $res = $this->actingAs($citizen)->post('/admin/reports/1/status', [
            'status' => 'completed',
        ]);
        $res->assertStatus(403);
    }

    public function test_citizen_only_sees_their_own_reports_on_web(): void
    {
        $category = \App\Models\WasteCategory::first();

        $citizen1 = User::create([
            'name' => 'Warga Satu',
            'email' => 'warga1',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);
        $citizen1->assignRole('user');

        $citizen2 = User::create([
            'name' => 'Warga Dua',
            'email' => 'warga2',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);
        $citizen2->assignRole('user');

        \App\Models\Report::create([
            'tracking_code' => 'SWM-20261103-AAAAA',
            'user_id' => $citizen1->id,
            'category_id' => $category->id,
            'description' => 'Sampah botol plastik Warga 1',
            'photo_path' => 'reports/test1.jpg',
            'latitude' => -6.2,
            'longitude' => 106.8,
            'address' => 'Jl. Mawar',
            'status' => 'pending',
        ]);

        \App\Models\Report::create([
            'tracking_code' => 'SWM-20261103-BBBBB',
            'user_id' => $citizen2->id,
            'category_id' => $category->id,
            'description' => 'Sampah kardus Warga 2',
            'photo_path' => 'reports/test2.jpg',
            'latitude' => -6.2,
            'longitude' => 106.8,
            'address' => 'Jl. Melati',
            'status' => 'pending',
        ]);

        // Citizen 1 viewing reports
        $response = $this->actingAs($citizen1)->get('/admin/reports');
        $response->assertStatus(200)
            ->assertSee('SWM-20261103-AAAAA')
            ->assertDontSee('SWM-20261103-BBBBB');

        // Citizen 1 viewing dashboard
        $dashResponse = $this->actingAs($citizen1)->get('/admin/dashboard');
        $dashResponse->assertStatus(200)
            ->assertSee('Dashboard Warga')
            ->assertSee('Total Laporan Saya');
    }

    public function test_api_docs_only_accessible_by_admin(): void
    {
        $admin = User::where('email', 'admin')->first();

        $citizen = User::create([
            'name' => 'Warga Test Docs',
            'email' => 'warga_test_docs',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);
        $citizen->assignRole('user');

        // Guest accessing /docs/api should redirect to login
        $guestRes = $this->get('/docs/api');
        $guestRes->assertRedirect('/admin/login');

        // Citizen accessing /docs/api should get 403 Forbidden
        $citizenRes = $this->actingAs($citizen)->get('/docs/api');
        $citizenRes->assertStatus(403);

        // Citizen sidebar does not show Dokumentasi API
        $citizenDash = $this->actingAs($citizen)->get('/admin/dashboard');
        $citizenDash->assertStatus(200)->assertDontSee('Dokumentasi API');

        // Admin accessing /docs/api should succeed
        $adminRes = $this->actingAs($admin)->get('/docs/api');
        $adminRes->assertStatus(200);

        // Admin sidebar shows Dokumentasi API
        $adminDash = $this->actingAs($admin)->get('/admin/dashboard');
        $adminDash->assertStatus(200)->assertSee('Dokumentasi API');
    }

    public function test_admin_can_soft_delete_report(): void
    {
        $admin = User::where('email', 'admin')->first();
        $category = \App\Models\WasteCategory::first();

        $report = \App\Models\Report::create([
            'tracking_code' => 'SWM-20261103-DEL01',
            'user_id' => $admin->id,
            'category_id' => $category->id,
            'description' => 'Sampah untuk dihapus',
            'photo_path' => 'reports/test_del.jpg',
            'latitude' => -6.2,
            'longitude' => 106.8,
            'address' => 'Jl. Kenanga',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->delete("/admin/reports/{$report->id}");
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('reports', ['id' => $report->id]);
    }

    public function test_citizen_can_delete_own_pending_report(): void
    {
        $citizen = User::create([
            'name' => 'Warga Test',
            'email' => 'warga_test_del_pending@example.com',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);
        $citizen->assignRole('user');

        $category = \App\Models\WasteCategory::first();
        $report = \App\Models\Report::create([
            'tracking_code' => 'SWM-20261103-DEL02',
            'user_id' => $citizen->id,
            'category_id' => $category->id,
            'description' => 'Sampah warga pending boleh dihapus sendiri',
            'photo_path' => 'reports/test_del2.jpg',
            'latitude' => -6.2,
            'longitude' => 106.8,
            'address' => 'Jl. Kenanga',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($citizen)->delete("/admin/reports/{$report->id}");
        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertSoftDeleted('reports', ['id' => $report->id]);
    }

    public function test_citizen_cannot_delete_other_citizens_report(): void
    {
        $citizen1 = User::create([
            'name' => 'Warga 1',
            'email' => 'warga1_del@example.com',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);
        $citizen1->assignRole('user');

        $citizen2 = User::create([
            'name' => 'Warga 2',
            'email' => 'warga2_del@example.com',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);
        $citizen2->assignRole('user');

        $category = \App\Models\WasteCategory::first();
        $report = \App\Models\Report::create([
            'tracking_code' => 'SWM-20261103-DEL03',
            'user_id' => $citizen1->id,
            'category_id' => $category->id,
            'description' => 'Laporan milik warga 1',
            'photo_path' => 'reports/test_del3.jpg',
            'latitude' => -6.2,
            'longitude' => 106.8,
            'address' => 'Jl. Kenanga',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($citizen2)->delete("/admin/reports/{$report->id}");
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('reports', ['id' => $report->id, 'deleted_at' => null]);
    }

    public function test_citizen_cannot_delete_in_progress_report(): void
    {
        $citizen = User::create([
            'name' => 'Warga 3',
            'email' => 'warga3_del@example.com',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);
        $citizen->assignRole('user');

        $category = \App\Models\WasteCategory::first();
        $report = \App\Models\Report::create([
            'tracking_code' => 'SWM-20261103-DEL04',
            'user_id' => $citizen->id,
            'category_id' => $category->id,
            'description' => 'Laporan in progress tidak boleh dihapus',
            'photo_path' => 'reports/test_del4.jpg',
            'latitude' => -6.2,
            'longitude' => 106.8,
            'address' => 'Jl. Kenanga',
            'status' => 'in_progress',
        ]);

        $response = $this->actingAs($citizen)->delete("/admin/reports/{$report->id}");
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('reports', ['id' => $report->id, 'deleted_at' => null]);
    }

    public function test_citizen_can_view_create_report_page(): void
    {
        $citizen = User::create([
            'name' => 'Warga Form Test',
            'email' => 'warga_form_test',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);
        $citizen->assignRole('user');

        $response = $this->actingAs($citizen)->get('/admin/reports/create');
        $response->assertStatus(200);
        $response->assertSee('Buat Laporan Baru');
        $response->assertSee('Klasifikasi Otomatis AI (MobileNetV2)');
    }

    public function test_admin_can_create_new_citizen_user(): void
    {
        $admin = User::where('email', 'admin')->first();

        $response = $this->actingAs($admin)->post('/admin/users', [
            'name' => 'Warga Baru Ditambahkan',
            'email' => 'warga_baru_test',
            'phone_number' => '081233445566',
            'password' => '12345678',
            'role' => 'user',
            'is_active' => '1',
        ]);

        $response->assertRedirect('/admin/users');
        $response->assertSessionHas('success');

        $user = User::where('email', 'warga_baru_test')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Warga Baru Ditambahkan', $user->name);
        $this->assertTrue($user->hasRole('user'));
        $this->assertTrue($user->is_active);
    }

    public function test_admin_can_create_new_admin_user(): void
    {
        $admin = User::where('email', 'admin')->first();

        $response = $this->actingAs($admin)->post('/admin/users', [
            'name' => 'Admin Baru Ditambahkan',
            'email' => 'admin_baru_test',
            'phone_number' => '089988776655',
            'password' => '12345678',
            'role' => 'admin',
            'is_active' => '1',
        ]);

        $response->assertRedirect('/admin/users');
        $response->assertSessionHas('success');

        $newAdmin = User::where('email', 'admin_baru_test')->first();
        $this->assertNotNull($newAdmin);
        $this->assertTrue($newAdmin->hasRole('admin'));
    }

    public function test_duplicate_email_fails_validation_when_creating_user(): void
    {
        $admin = User::where('email', 'admin')->first();

        $response = $this->actingAs($admin)->post('/admin/users', [
            'name' => 'Duplikat Admin',
            'email' => 'admin', // already exists
            'password' => '12345678',
            'role' => 'user',
        ]);

        $response->assertSessionHasErrors(['email']);
    }
}
