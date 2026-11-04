<?php

namespace Tests\Feature;

use App\Models\Report;
use App\Models\User;
use App\Models\WasteCategory;
use App\Services\ReportService;
use Database\Seeders\RoleSeeder;
use Database\Seeders\WasteCategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReportApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $admin;
    protected WasteCategory $category;
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(WasteCategorySeeder::class);

        $this->user = User::create([
            'name' => 'Warga Pelapor',
            'email' => 'warga.test@gmail.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);
        $this->user->assignRole('user');
        $this->token = $this->user->createToken('test_token')->plainTextToken;

        $this->admin = User::create([
            'name' => 'Admin Penguji',
            'email' => 'admin.test@banksampah.id',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);
        $this->admin->assignRole('admin');

        $this->category = WasteCategory::first();
    }

    public function test_user_can_submit_report_with_image_upload(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('waste_sample.jpg', 600, 600);

        $payload = [
            'photo' => $file,
            'category_id' => $this->category->id,
            'ai_predicted_category' => 'Plastic Waste',
            'ai_confidence_score' => 0.9450,
            'description' => 'Tumpukan sampah plastik di selokan depan rumah warga.',
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'address' => 'Jl. Sudirman No. 10, Jakarta Pusat',
            'scale' => 'medium',
            'urgency' => 'high',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/v1/reports', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Laporan sampah berhasil dikirimkan.',
            ]);

        $this->assertDatabaseHas('reports', [
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'status' => 'pending',
        ]);

        $report = Report::where('user_id', $this->user->id)->first();
        $this->assertNotNull($report);
        $this->assertStringStartsWith('SWM-', $report->tracking_code);

        // Verify initial audit log created
        $this->assertDatabaseHas('report_status_logs', [
            'report_id' => $report->id,
            'from_status' => 'pending',
            'to_status' => 'pending',
        ]);

        Storage::disk('public')->assertExists($report->photo_path);
    }

    public function test_status_can_be_updated_from_completed_and_logged_in_history(): void
    {
        $report = Report::create([
            'tracking_code' => 'SWM-TEST-001',
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'description' => 'Sampah daun kering',
            'photo_path' => 'reports/test.jpg',
            'latitude' => -6.2000,
            'longitude' => 106.8000,
            'address' => 'Jl. Test No. 1',
            'status' => 'completed',
        ]);

        $service = app(ReportService::class);
        $result = $service->updateStatus($report, 'in_progress', $this->admin, 'Tinjau ulang di lapangan');

        $this->assertTrue($result->isSuccess());
        $this->assertEquals('in_progress', $report->fresh()->status);
        $this->assertDatabaseHas('report_status_logs', [
            'report_id' => $report->id,
            'changed_by_user_id' => $this->admin->id,
            'from_status' => 'completed',
            'to_status' => 'in_progress',
            'notes' => 'Tinjau ulang di lapangan',
        ]);
    }

    public function test_rejection_requires_minimum_5_characters_note(): void
    {
        $report = Report::create([
            'tracking_code' => 'SWM-TEST-002',
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'description' => 'Sampah liar',
            'photo_path' => 'reports/test.jpg',
            'latitude' => -6.2000,
            'longitude' => 106.8000,
            'address' => 'Jl. Test No. 2',
            'status' => 'pending',
        ]);

        $service = app(ReportService::class);
        
        // Note less than 5 characters
        $resInvalid = $service->updateStatus($report, 'rejected', $this->admin, 'bad');
        $this->assertFalse($resInvalid->isSuccess());
        $this->assertEquals(422, $resInvalid->statusCode);

        // Valid note with at least 5 characters
        $resValid = $service->updateStatus($report, 'rejected', $this->admin, 'Lokasi sudah bersih');
        $this->assertTrue($resValid->isSuccess());
        $this->assertEquals('rejected', $report->fresh()->status);
        $this->assertEquals('Lokasi sudah bersih', $report->fresh()->rejection_note);
    }
}
