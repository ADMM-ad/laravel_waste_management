<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\WasteCategory;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'category_id', 'status', 'urgency', 'start_date', 'end_date']);
        if ($request->user()->hasRole('user')) {
            $filters['user_id'] = $request->user()->id;
        }

        $reports = $this->reportService->getFilteredReports($filters, 15);
        $categories = WasteCategory::all();

        return view('admin.reports.index', compact('reports', 'categories', 'filters'));
    }

    public function show(int $id, Request $request): JsonResponse
    {
        $userId = $request->user()->hasRole('user') ? $request->user()->id : null;
        $result = $this->reportService->getReportDetail($id, $userId);
        if (! $result->isSuccess()) {
            return response()->json(['success' => false, 'message' => $result->message], 404);
        }

        return response()->json(['success' => true, 'data' => $result->data]);
    }

    public function updateStatus(int $id, Request $request): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,rejected',
            'notes' => 'nullable|string',
        ]);

        $report = Report::findOrFail($id);
        $result = $this->reportService->updateStatus(
            $report,
            $request->input('status'),
            $request->user(),
            $request->input('notes')
        );

        if (! $result->isSuccess()) {
            return back()->with('error', $result->message);
        }

        return back()->with('success', $result->message);
    }

    public function create(Request $request): View|RedirectResponse
    {
        if ($request->user()->hasRole('admin')) {
            return redirect()->route('admin.reports.index')->with('error', 'Pembuatan laporan baru dilakukan oleh warga (role user).');
        }

        $categories = WasteCategory::all();
        return view('admin.reports.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasRole('admin')) {
            return redirect()->route('admin.reports.index')->with('error', 'Pembuatan laporan baru dilakukan oleh warga (role user).');
        }

        $validated = $request->validate([
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
            'photo.required' => 'Foto bukti kondisi sampah wajib diunggah.',
            'photo.mimes' => 'Format foto harus berupa jpeg, jpg, png, atau webp.',
            'photo.max' => 'Ukuran foto maksimal 5MB.',
            'category_id.required' => 'Pilih kategori sampah yang sesuai.',
            'description.required' => 'Deskripsi laporan wajib diisi.',
            'description.min' => 'Deskripsi laporan minimal 10 karakter.',
            'latitude.required' => 'Titik koordinat latitude wajib terisi.',
            'longitude.required' => 'Titik koordinat longitude wajib terisi.',
            'address.required' => 'Alamat atau patokan jalan wajib diisi.',
            'scale.required' => 'Skala volume sampah wajib dipilih.',
            'urgency.required' => 'Tingkat urgensi wajib dipilih.',
        ]);

        $result = $this->reportService->createReport($validated, $request->file('photo'), $request->user());

        if (! $result->isSuccess()) {
            return back()->withInput()->with('error', $result->message);
        }

        return redirect()->route('admin.reports.index')->with('success', 'Laporan sampah berhasil dibuat dengan kode: ' . $result->data->tracking_code);
    }

    public function edit(int $id, Request $request): View|RedirectResponse
    {
        $report = Report::findOrFail($id);

        if ($report->user_id !== $request->user()->id && ! $request->user()->hasRole('admin')) {
            return redirect()->route('admin.reports.index')->with('error', 'Anda tidak memiliki hak untuk mengedit laporan ini.');
        }

        if ($report->status !== 'pending') {
            return redirect()->route('admin.reports.index')->with('error', 'Hanya laporan dengan status pending yang dapat diedit.');
        }

        $categories = WasteCategory::all();
        return view('admin.reports.edit', compact('report', 'categories'));
    }

    public function update(int $id, Request $request): RedirectResponse
    {
        $report = Report::findOrFail($id);

        $validated = $request->validate([
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
            'photo.mimes' => 'Format foto harus berupa jpeg, jpg, png, atau webp.',
            'photo.max' => 'Ukuran foto maksimal 5MB.',
            'category_id.required' => 'Pilih kategori sampah yang sesuai.',
            'description.required' => 'Deskripsi laporan wajib diisi.',
            'description.min' => 'Deskripsi laporan minimal 10 karakter.',
            'latitude.required' => 'Titik koordinat latitude wajib terisi.',
            'longitude.required' => 'Titik koordinat longitude wajib terisi.',
            'address.required' => 'Alamat atau patokan jalan wajib diisi.',
            'scale.required' => 'Skala volume sampah wajib dipilih.',
            'urgency.required' => 'Tingkat urgensi wajib dipilih.',
        ]);

        $result = $this->reportService->updateReport($report, $validated, $request->file('photo'), $request->user());

        if (! $result->isSuccess()) {
            return back()->withInput()->with('error', $result->message);
        }

        return redirect()->route('admin.reports.index')->with('success', 'Laporan ' . $report->tracking_code . ' berhasil diperbarui.');
    }

    public function classifyImage(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|file|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $fastApiUrl = rtrim(config('services.fastapi.url', 'http://127.0.0.1:8000'), '/');

        try {
            $file = $request->file('image');
            $response = Http::timeout(8.0)
                ->attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
                ->post("{$fastApiUrl}/predict");

            if ($response->successful()) {
                $fastApiData = $response->json();
                // FastAPI returns {"success": true, "message": "...", "data": {"predicted_class": ..., "confidence_score": ...}}
                $predictionData = isset($fastApiData['data']) && is_array($fastApiData['data']) ? $fastApiData['data'] : $fastApiData;

                return response()->json([
                    'success' => true,
                    'data' => $predictionData,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Service Machine Learning merespons dengan kesalahan: ' . $response->status(),
            ], 502);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal terhubung ke Service Machine Learning di ' . $fastApiUrl . '. Pastikan service aktif.',
            ], 503);
        }
    }

    public function destroy(int $id, Request $request): RedirectResponse
    {
        $report = Report::findOrFail($id);
        $result = $this->reportService->deleteReport($report, $request->user());

        if (! $result->isSuccess()) {
            return back()->with('error', $result->message);
        }

        return back()->with('success', $result->message);
    }
}
