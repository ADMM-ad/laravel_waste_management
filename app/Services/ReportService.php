<?php

namespace App\Services;

use App\DTOs\ServiceResult;
use App\Models\Report;
use App\Models\ReportStatusLog;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReportService
{
    /**
     * Create a new waste report with photo upload, tracking code, and initial audit log.
     */
    public function createReport(array $data, UploadedFile $photo, User $user): ServiceResult
    {
        // 1. Validate file constraints
        $allowedExtensions = ['jpeg', 'jpg', 'png', 'webp'];
        $extension = strtolower($photo->getClientOriginalExtension());
        if (! in_array($extension, $allowedExtensions)) {
            return ServiceResult::error('Format gambar tidak didukung. Gunakan jpeg, jpg, png, atau webp.', 422);
        }

        if ($photo->getSize() > 5 * 1024 * 1024) {
            return ServiceResult::error('Ukuran gambar maksimal adalah 5MB.', 422);
        }

        try {
            // 2. Store photo with unique uuid
            $fileName = Str::uuid() . '.' . $extension;
            $photoPath = $photo->storeAs('reports', $fileName, 'public');

            // 3. Generate unique tracking code: SWM-YYYYMMDD-XXXXX
            do {
                $trackingCode = 'SWM-' . date('Ymd') . '-' . strtoupper(Str::random(5));
            } while (Report::where('tracking_code', $trackingCode)->exists());

            // 4. DB Transaction
            $report = DB::transaction(function () use ($data, $photoPath, $trackingCode, $user) {
                $newReport = Report::create([
                    'tracking_code' => $trackingCode,
                    'user_id' => $user->id,
                    'category_id' => $data['category_id'],
                    'ai_predicted_category' => $data['ai_predicted_category'] ?? null,
                    'ai_confidence_score' => isset($data['ai_confidence_score']) ? floatval($data['ai_confidence_score']) : null,
                    'description' => $data['description'],
                    'photo_path' => $photoPath,
                    'latitude' => floatval($data['latitude']),
                    'longitude' => floatval($data['longitude']),
                    'address' => $data['address'],
                    'scale' => $data['scale'] ?? 'small',
                    'urgency' => $data['urgency'] ?? 'medium',
                    'status' => 'pending',
                ]);

                // Initial audit trail log
                ReportStatusLog::create([
                    'report_id' => $newReport->id,
                    'changed_by_user_id' => $user->id,
                    'from_status' => 'pending',
                    'to_status' => 'pending',
                    'notes' => 'Laporan pertama dibuat oleh pelapor.',
                ]);

                return $newReport;
            });

            $report->load(['category', 'user', 'statusLogs']);

            return ServiceResult::success($report, 'Laporan sampah berhasil dikirimkan.', 201);
        } catch (\Throwable $e) {
            return ServiceResult::error('Gagal menyimpan laporan: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Mutate report status with strict terminal-state rules and audit trail logging.
     */
    public function updateStatus(Report $report, string $newStatus, User $admin, ?string $note): ServiceResult
    {
        $allowedStatuses = ['pending', 'in_progress', 'completed', 'rejected'];
        if (! in_array($newStatus, $allowedStatuses)) {
            return ServiceResult::error("Status '{$newStatus}' tidak valid.", 422);
        }

        // Terminal state validation
        if (in_array($report->status, ['completed', 'rejected'])) {
            return ServiceResult::error(
                "Laporan dengan status '{$report->status}' telah berstatus akhir dan tidak dapat diubah lagi.",
                422
            );
        }

        // Rejection note rule
        if ($newStatus === 'rejected') {
            if (empty(trim($note ?? '')) || strlen(trim($note ?? '')) < 5) {
                return ServiceResult::error(
                    'Catatan penolakan wajib diisi minimal 5 karakter untuk status Rejected.',
                    422,
                    ['rejection_note' => ['Catatan penolakan minimal 5 karakter']]
                );
            }
        }

        try {
            DB::transaction(function () use ($report, $newStatus, $admin, $note) {
                $oldStatus = $report->status;
                $report->status = $newStatus;
                if ($newStatus === 'rejected') {
                    $report->rejection_note = trim($note);
                }
                $report->save();

                ReportStatusLog::create([
                    'report_id' => $report->id,
                    'changed_by_user_id' => $admin->id,
                    'from_status' => $oldStatus,
                    'to_status' => $newStatus,
                    'notes' => $note ?: "Status diubah dari {$oldStatus} ke {$newStatus}.",
                ]);
            });

            $report->refresh()->load(['category', 'user', 'statusLogs.changedByUser']);

            return ServiceResult::success($report, "Status laporan berhasil diperbarui menjadi '{$newStatus}'.");
        } catch (\Throwable $e) {
            return ServiceResult::error('Gagal memperbarui status laporan: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Retrieve paginated reports with search and multi-facet filtering.
     */
    public function getFilteredReports(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Report::with(['category', 'user', 'statusLogs.changedByUser'])
            ->latest('created_at');

        // Filter user_id (for citizen mobile history)
        if (! empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        // Filter status
        if (! empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        // Filter category_id
        if (! empty($filters['category_id']) && $filters['category_id'] !== 'all') {
            $query->where('category_id', $filters['category_id']);
        }

        // Filter urgency
        if (! empty($filters['urgency']) && $filters['urgency'] !== 'all') {
            $query->where('urgency', $filters['urgency']);
        }

        // Search tracking_code or address
        if (! empty($filters['search'])) {
            $search = '%' . trim($filters['search']) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('tracking_code', 'like', $search)
                  ->orWhere('address', 'like', $search)
                  ->orWhere('description', 'like', $search);
            });
        }

        // Date range
        if (! empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }
        if (! empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        return $query->paginate($perPage);
    }

    /**
     * Get specific report detail.
     */
    public function getReportDetail(int $id, ?int $userId = null): ServiceResult
    {
        $query = Report::with(['category', 'user', 'statusLogs.changedByUser']);
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $report = $query->find($id);
        if (! $report) {
            return ServiceResult::error('Laporan tidak ditemukan atau Anda tidak memiliki akses.', 404);
        }

        return ServiceResult::success($report, 'Detail laporan ditemukan.');
    }
}
