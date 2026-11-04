<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Services\ReportService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected ReportService $reportService
    ) {}

    /**
     * Get paginated reports for the authenticated citizen user.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'search', 'start_date', 'end_date', 'category_id', 'urgency']);
        $filters['user_id'] = $request->user()->id;

        $perPage = (int) $request->input('per_page', 15);
        $paginator = $this->reportService->getFilteredReports($filters, $perPage);

        return $this->jsonResponse(true, 'Daftar riwayat laporan berhasil diambil.', $paginator);
    }

    /**
     * Get specific report detail belonging to the authenticated user.
     */
    public function show(int $id, Request $request): JsonResponse
    {
        $result = $this->reportService->getReportDetail($id, $request->user()->id);
        return $this->serviceResponse($result);
    }

    /**
     * Submit a new waste report with image upload.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'photo' => 'required|file|mimes:jpeg,png,jpg,webp|max:5120',
            'category_id' => 'required|integer|exists:waste_categories,id',
            'ai_predicted_category' => 'nullable|string|max:100',
            'ai_confidence_score' => 'nullable|numeric|between:0,1',
            'description' => 'required|string|min:10',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'address' => 'required|string',
            'scale' => 'required|in:small,medium,large',
            'urgency' => 'required|in:low,medium,high',
        ], [
            'photo.required' => 'Foto sampah wajib diunggah.',
            'photo.file' => 'Berkas foto tidak valid.',
            'photo.mimes' => 'Format foto harus berupa jpeg, jpg, png, atau webp.',
            'photo.max' => 'Ukuran foto maksimal adalah 5MB.',
            'category_id.required' => 'Kategori sampah wajib dipilih.',
            'category_id.exists' => 'Kategori sampah yang dipilih tidak valid.',
            'description.required' => 'Deskripsi laporan wajib diisi.',
            'description.min' => 'Deskripsi laporan minimal 10 karakter.',
            'latitude.required' => 'Titik koordinat latitude wajib terdeteksi.',
            'longitude.required' => 'Titik koordinat longitude wajib terdeteksi.',
            'address.required' => 'Alamat lokasi laporan wajib diisi.',
            'scale.required' => 'Skala volume sampah wajib dipilih.',
            'scale.in' => 'Skala volume harus berupa small, medium, atau large.',
            'urgency.required' => 'Tingkat urgensi laporan wajib dipilih.',
            'urgency.in' => 'Tingkat urgensi harus berupa low, medium, atau high.',
        ]);

        if ($validator->fails()) {
            return $this->jsonResponse(false, 'Validasi formulir laporan gagal.', null, 422, $validator->errors()->toArray());
        }

        $result = $this->reportService->createReport(
            $validator->validated(),
            $request->file('photo'),
            $request->user()
        );

        return $this->serviceResponse($result);
    }

    /**
     * Update existing pending report by citizen owner.
     */
    public function update(int $id, Request $request): JsonResponse
    {
        $report = Report::find($id);
        if (! $report) {
            return $this->jsonResponse(false, 'Laporan tidak ditemukan.', null, 404);
        }

        $validator = Validator::make($request->all(), [
            'photo' => 'nullable|file|mimes:jpeg,png,jpg,webp|max:5120',
            'category_id' => 'required|integer|exists:waste_categories,id',
            'ai_predicted_category' => 'nullable|string|max:100',
            'ai_confidence_score' => 'nullable|numeric|between:0,1',
            'description' => 'required|string|min:10',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'address' => 'required|string',
            'scale' => 'required|in:small,medium,large',
            'urgency' => 'required|in:low,medium,high',
        ], [
            'photo.file' => 'Berkas foto tidak valid.',
            'photo.mimes' => 'Format foto harus berupa jpeg, jpg, png, atau webp.',
            'photo.max' => 'Ukuran foto maksimal adalah 5MB.',
            'category_id.required' => 'Kategori sampah wajib dipilih.',
            'category_id.exists' => 'Kategori sampah yang dipilih tidak valid.',
            'description.required' => 'Deskripsi laporan wajib diisi.',
            'description.min' => 'Deskripsi laporan minimal 10 karakter.',
            'latitude.required' => 'Titik koordinat latitude wajib terdeteksi.',
            'longitude.required' => 'Titik koordinat longitude wajib terdeteksi.',
            'address.required' => 'Alamat lokasi laporan wajib diisi.',
            'scale.required' => 'Skala volume sampah wajib dipilih.',
            'scale.in' => 'Skala volume harus berupa small, medium, atau large.',
            'urgency.required' => 'Tingkat urgensi laporan wajib dipilih.',
            'urgency.in' => 'Tingkat urgensi harus berupa low, medium, atau high.',
        ]);

        if ($validator->fails()) {
            return $this->jsonResponse(false, 'Validasi update laporan gagal.', null, 422, $validator->errors()->toArray());
        }

        $result = $this->reportService->updateReport(
            $report,
            $validator->validated(),
            $request->file('photo'),
            $request->user()
        );

        return $this->serviceResponse($result);
    }

    /**
     * Delete pending report by citizen owner or admin.
     */
    public function destroy(int $id, Request $request): JsonResponse
    {
        $report = Report::find($id);
        if (! $report) {
            return $this->jsonResponse(false, 'Laporan tidak ditemukan.', null, 404);
        }

        $result = $this->reportService->deleteReport($report, $request->user());
        return $this->serviceResponse($result);
    }
}
