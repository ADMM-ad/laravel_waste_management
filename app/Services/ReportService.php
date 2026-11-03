<?php

namespace App\Services;

use App\DTOs\ServiceResult;
use App\Models\Report;
use App\Models\ReportStatusLog;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
            // 2. Store photo with unique uuid and auto-compression
            $fileName = Str::uuid() . '.' . $extension;
            $photoPath = $this->compressAndStorePhoto($photo, $fileName);

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

    /**
     * Soft delete a waste report (Admin only).
     */
    public function deleteReport(Report $report, User $currentUser): ServiceResult
    {
        if (! $currentUser->hasRole('admin')) {
            return ServiceResult::error('Hanya Administrator yang memiliki wewenang menghapus laporan.', 403);
        }

        try {
            $trackingCode = $report->tracking_code;
            $report->delete();

            return ServiceResult::success(null, "Laporan {$trackingCode} berhasil dihapus.");
        } catch (\Throwable $e) {
            return ServiceResult::error('Gagal menghapus laporan: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Auto-resize and compress uploaded photo to ensure it stays lightweight and under storage limits.
     */
    private function compressAndStorePhoto(UploadedFile $photo, string $fileName): string
    {
        $extension = strtolower($photo->getClientOriginalExtension());
        $path = "reports/{$fileName}";

        if (extension_loaded('gd')) {
            try {
                $imagePath = $photo->getRealPath();
                $image = null;

                switch ($extension) {
                    case 'jpeg':
                    case 'jpg':
                        $image = @imagecreatefromjpeg($imagePath);
                        break;
                    case 'png':
                        $image = @imagecreatefrompng($imagePath);
                        break;
                    case 'webp':
                        if (function_exists('imagecreatefromwebp')) {
                            $image = @imagecreatefromwebp($imagePath);
                        }
                        break;
                }

                if ($image !== null && $image !== false) {
                    $width = imagesx($image);
                    $height = imagesy($image);
                    $maxDimension = 1600;

                    // Resize proportionally if larger than maxDimension
                    if ($width > $maxDimension || $height > $maxDimension) {
                        if ($width > $height) {
                            $newWidth = $maxDimension;
                            $newHeight = (int) round(($height / $width) * $maxDimension);
                        } else {
                            $newHeight = $maxDimension;
                            $newWidth = (int) round(($width / $height) * $maxDimension);
                        }

                        $resized = imagecreatetruecolor($newWidth, $newHeight);
                        if ($extension === 'png') {
                            imagealphablending($resized, false);
                            imagesavealpha($resized, true);
                        }
                        imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                        imagedestroy($image);
                        $image = $resized;
                    }

                    ob_start();
                    if ($extension === 'png') {
                        imagepng($image, null, 6);
                    } elseif ($extension === 'webp' && function_exists('imagewebp')) {
                        imagewebp($image, null, 80);
                    } else {
                        imagejpeg($image, null, 80);
                    }
                    $compressedData = ob_get_clean();
                    imagedestroy($image);

                    if ($compressedData !== false && strlen($compressedData) > 0) {
                        Storage::disk('public')->put($path, $compressedData);
                        return $path;
                    }
                }
            } catch (\Throwable $e) {
                // Fallback to default storage if GD fails
            }
        }

        return $photo->storeAs('reports', $fileName, 'public');
    }
}
