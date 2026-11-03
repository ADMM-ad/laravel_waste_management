<?php

namespace App\Services;

use App\Models\Report;
use App\Models\ReportStatusLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardStatisticService
{
    /**
     * Compute dashboard analytics metrics, trend line, urgency breakdown, scale breakdown, and recent status logs.
     */
    public function getStatistics(array $filters = [], ?User $currentUser = null): array
    {
        $isCitizen = $currentUser && $currentUser->hasRole('user');
        $reportQuery = Report::query();

        if ($isCitizen) {
            $reportQuery->where('user_id', $currentUser->id);
        }

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
        $totalUsers = $isCitizen ? $totalCompleted : User::role('user')->count();

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

        $recentTrendQuery = Report::where('created_at', '>=', $startDate30Days);
        if ($isCitizen) {
            $recentTrendQuery->where('user_id', $currentUser->id);
        }

        $recentReports = $recentTrendQuery
            ->select(DB::raw("$dateExpression as date"), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        foreach ($recentReports as $dateKey => $countVal) {
            if (isset($trendData[$dateKey])) {
                $trendData[$dateKey] = (int) $countVal;
            }
        }

        // 3. Urgency Distribution (Donut Chart)
        $urgencyQuery = clone $reportQuery;
        $urgencyDistribution = [
            'labels' => ['Tinggi', 'Sedang', 'Rendah'],
            'values' => [
                (clone $urgencyQuery)->where('urgency', 'high')->count(),
                (clone $urgencyQuery)->where('urgency', 'medium')->count(),
                (clone $urgencyQuery)->where('urgency', 'low')->count(),
            ],
            'colors' => ['#DC2626', '#D97706', '#64748B'],
        ];

        // 4. Scale / Volume Breakdown
        $scaleDistribution = [
            'large' => (clone $urgencyQuery)->where('scale', 'large')->count(),
            'medium' => (clone $urgencyQuery)->where('scale', 'medium')->count(),
            'small' => (clone $urgencyQuery)->where('scale', 'small')->count(),
        ];

        // 5. Recent Status Logs (Penanganan Sampah Terbaru)
        $statusLogsQuery = ReportStatusLog::with(['report', 'changedByUser'])
            ->latest('created_at')
            ->limit(5);

        if ($isCitizen) {
            $statusLogsQuery->whereHas('report', fn($q) => $q->where('user_id', $currentUser->id));
        }

        $recentStatusLogs = $statusLogsQuery->get();

        return [
            'is_citizen' => $isCitizen,
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
            'urgency_distribution' => $urgencyDistribution,
            'scale_distribution' => $scaleDistribution,
            'recent_status_logs' => $recentStatusLogs,
        ];
    }
}
