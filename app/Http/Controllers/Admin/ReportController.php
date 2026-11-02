<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\WasteCategory;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'category_id', 'status', 'urgency', 'start_date', 'end_date']);
        $reports = $this->reportService->getFilteredReports($filters, 15);
        $categories = WasteCategory::all();

        return view('admin.reports.index', compact('reports', 'categories', 'filters'));
    }

    public function show(int $id): JsonResponse
    {
        $result = $this->reportService->getReportDetail($id);
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
}
