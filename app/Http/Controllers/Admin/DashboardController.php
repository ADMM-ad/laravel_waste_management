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
        $filters = $request->only(['start_date', 'end_date', 'category_id']);
        $stats = $this->statisticService->getStatistics($filters, $request->user());
        $categories = WasteCategory::all();

        return view('admin.dashboard.index', compact('stats', 'categories', 'filters'));
    }
}
