<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WasteCategory;
use App\Services\DashboardStatisticService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardStatisticService $statisticService
    ) {}

    public function index(Request $request): View
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $categoryId = $request->input('category_id');

        $filters = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'category_id' => $categoryId,
        ];

        $stats = $this->statisticService->getStatistics($filters, $request->user());
        $categories = WasteCategory::all();

        return view('admin.dashboard.index', compact('stats', 'categories', 'filters'));
    }
}
