<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
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
        $filters = $request->only(['status', 'search']);
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
}
