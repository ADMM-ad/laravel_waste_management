<?php

namespace App\Services;

use App\Models\Report;
use App\Models\User;
use App\Models\WasteCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardStatisticService
{
    /**
     * Compute dashboard analytics metrics, trend line, and category breakdown.
     */
    public function getStatistics(array $filters = []): array
    {
        $reportQuery = Report::query();

        if (! empty($filters['category_id']) && $filters['category_id'] !== 'all') {
            $reportQuery->where('category_id', $filters['category_id']);
        }

        if (! empty($filters['start_date'])) {
            $reportQuery->whereDate('created_at', '>=', $filters['start_date']);
        }

        if (! empty($filters['end_date'])) {
            $reportQuery->whereDate('created_at', '<=', $filters['end_date']);
        }

        // 1. Metric Cards
        $totalReports = (clone $reportQuery)->count();
        $totalPending = (clone $reportQuery)->where('status', 'pending')->count();
        $totalInProgress = (clone $reportQuery)->where('status', 'in_progress')->count();
        $totalCompleted = (clone $reportQuery)->where('status', 'completed')->count();
        $totalRejected = (clone $reportQuery)->where('status', 'rejected')->count();
        $totalUsers = User::role('user')->count();

        // 2. 30 Days Trend Line
        $trendData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $trendData[$date] = 0;
        }

        $startDate30Days = Carbon::now()->subDays(29)->startOfDay();
        $driver = DB::connection()->getDriverName();
        $dateExpression = $driver === 'sqlite'
            ? "strftime('%Y-%m-%d', created_at)"
            : "DATE_FORMAT(created_at, '%Y-%m-%d')";

        $recentReports = Report::where('created_at', '>=', $startDate30Days)
            ->select(DB::raw("$dateExpression as date"), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        foreach ($recentReports as $dateKey => $countVal) {
            if (isset($trendData[$dateKey])) {
                $trendData[$dateKey] = (int) $countVal;
            }
        }

        // 3. Category Distribution (Donut Chart)
        $categories = WasteCategory::withCount(['reports' => function ($q) use ($filters) {
            if (! empty($filters['start_date'])) {
                $q->whereDate('created_at', '>=', $filters['start_date']);
            }
            if (! empty($filters['end_date'])) {
                $q->whereDate('created_at', '<=', $filters['end_date']);
            }
        }])->get();

        $categoryDistribution = $categories->map(function ($cat) {
            return [
                'id' => $cat->id,
                'name' => $cat->name,
                'count' => $cat->reports_count,
            ];
        });

        return [
            'metrics' => [
                'total_reports' => $totalReports,
                'total_pending' => $totalPending,
                'total_in_progress' => $totalInProgress,
                'total_completed' => $totalCompleted,
                'total_rejected' => $totalRejected,
                'total_users' => $totalUsers,
            ],
            'trend_30_days' => [
                'labels' => array_keys($trendData),
                'values' => array_values($trendData),
            ],
            'category_distribution' => $categoryDistribution,
        ];
    }
}
