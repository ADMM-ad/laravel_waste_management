<?php

namespace Database\Seeders;

use App\Models\Report;
use App\Models\ReportStatusLog;
use App\Models\User;
use App\Models\WasteCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        $citizen = User::where('email', 'warga@banksampah.id')->first();
        $admin = User::where('email', 'admin@banksampah.id')->first();
        $organic = WasteCategory::where('name', 'Organic Waste')->first();
        $plastic = WasteCategory::where('name', 'Plastic Waste')->first();
        $paper = WasteCategory::where('name', 'Paper Waste')->first();

        if (! $citizen || ! $admin || ! $organic || ! $plastic || ! $paper) {
            return;
        }

        // Ensure a placeholder sample image exists
        $sampleImgPath = 'reports/sample_waste.jpg';
        if (! Storage::disk('public')->exists($sampleImgPath)) {
            // 1x1 transparent PNG / simple JPEG
            Storage::disk('public')->put($sampleImgPath, base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA='));
        }

        $sampleReports = [
            [
                'tracking_code' => 'SWM-' . date('Ymd') . '-A1001',
                'category_id' => $plastic->id,
                'ai_predicted_category' => 'Plastic Waste',
                'ai_confidence_score' => 0.9425,
                'description' => 'Tumpukan botol plastik air mineral dan gelas plastik di pinggir selokan jalan utama.',
                'photo_path' => $sampleImgPath,
                'latitude' => -6.2088,
                'longitude' => 106.8456,
                'address' => 'Jl. Sudirman No. 45, RT 02/RW 03, Kel. Menteng, Kec. Menteng, Jakarta Pusat',
                'scale' => 'medium',
                'urgency' => 'high',
                'status' => 'pending',
            ],
            [
                'tracking_code' => 'SWM-' . date('Ymd') . '-B2002',
                'category_id' => $organic->id,
                'ai_predicted_category' => 'Organic Waste',
                'ai_confidence_score' => 0.8870,
                'description' => 'Sisa sampah dedaunan dan sisa potongan pohon di trotoar taman kota.',
                'photo_path' => $sampleImgPath,
                'latitude' => -6.1754,
                'longitude' => 106.8272,
                'address' => 'Jl. Medan Merdeka Barat No. 12, Gambir, Jakarta Pusat',
                'scale' => 'large',
                'urgency' => 'medium',
                'status' => 'in_progress',
            ],
            [
                'tracking_code' => 'SWM-' . date('Ymd') . '-C3003',
                'category_id' => $paper->id,
                'ai_predicted_category' => 'Paper Waste',
                'ai_confidence_score' => 0.9610,
                'description' => 'Kardus bekas kemasan barang elektronik basah dekat TPS perumahan.',
                'photo_path' => $sampleImgPath,
                'latitude' => -6.2250,
                'longitude' => 106.8000,
                'address' => 'Jl. Senopati No. 88, Kebayoran Baru, Jakarta Selatan',
                'scale' => 'small',
                'urgency' => 'low',
                'status' => 'completed',
            ],
            [
                'tracking_code' => 'SWM-' . date('Ymd') . '-D4004',
                'category_id' => $plastic->id,
                'ai_predicted_category' => 'Plastic Waste',
                'ai_confidence_score' => 0.7250,
                'description' => 'Puing plastik dalam pekarangan rumah pribadi yang sudah ditangani pemilik.',
                'photo_path' => $sampleImgPath,
                'latitude' => -6.2100,
                'longitude' => 106.8500,
                'address' => 'Jl. Tebet Raya No. 15, Tebet, Jakarta Selatan',
                'scale' => 'small',
                'urgency' => 'low',
                'status' => 'rejected',
                'rejection_note' => 'Lokasi berada di properti pribadi dan telah dibersihkan mandiri oleh pemilik.',
            ],
        ];

        foreach ($sampleReports as $data) {
            $report = Report::firstOrCreate(
                ['tracking_code' => $data['tracking_code']],
                array_merge($data, ['user_id' => $citizen->id])
            );

            // Audit Trail Log 1: Created
            ReportStatusLog::firstOrCreate(
                [
                    'report_id' => $report->id,
                    'from_status' => 'pending',
                    'to_status' => 'pending',
                ],
                [
                    'changed_by_user_id' => $citizen->id,
                    'notes' => 'Laporan pertama dibuat oleh pelapor.',
                ]
            );

            // Additional logs for progressed reports
            if ($report->status === 'in_progress' || $report->status === 'completed') {
                ReportStatusLog::firstOrCreate(
                    [
                        'report_id' => $report->id,
                        'from_status' => 'pending',
                        'to_status' => 'in_progress',
                    ],
                    [
                        'changed_by_user_id' => $admin->id,
                        'notes' => 'Petugas telah ditugaskan menuju titik lokasi.',
                    ]
                );
            }

            if ($report->status === 'completed') {
                ReportStatusLog::firstOrCreate(
                    [
                        'report_id' => $report->id,
                        'from_status' => 'in_progress',
                        'to_status' => 'completed',
                    ],
                    [
                        'changed_by_user_id' => $admin->id,
                        'notes' => 'Sampah telah berhasil diangkut ke TPS terpadu.',
                    ]
                );
            }

            if ($report->status === 'rejected') {
                ReportStatusLog::firstOrCreate(
                    [
                        'report_id' => $report->id,
                        'from_status' => 'pending',
                        'to_status' => 'rejected',
                    ],
                    [
                        'changed_by_user_id' => $admin->id,
                        'notes' => $data['rejection_note'],
                    ]
                );
            }
        }
    }
}
