@extends('layouts.master')

@section('title', 'Dashboard Ikhtisar')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-foreground">{{ $stats['is_citizen'] ? 'Dashboard Warga' : 'Dashboard Operasional' }}</h1>
            <p class="text-xs sm:text-sm text-muted-foreground">{{ $stats['is_citizen'] ? 'Pemantauan status laporan sampah Anda, riwayat penanganan, dan volume kontribusi lingkungan.' : 'Pemantauan metrik laporan sampah, tren eskalasi, dan distribusi kategori secara real-time.' }}</p>
        </div>
    </div>

    <!-- Filter Toolbar Card -->
    <div class="rounded-xl border border-border bg-card p-3 sm:p-4 shadow-xs">
        <form method="GET" action="{{ route('admin.dashboard') }}" class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 items-center">
            <!-- Start Date -->
            <div class="relative">
                <span class="absolute left-3 top-2.5 text-xs font-medium text-muted-foreground">Dari:</span>
                <input type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}"
                       class="w-full rounded-md border border-input bg-background pl-12 pr-3 py-2 text-xs text-foreground shadow-xs focus:outline-none focus:ring-1 focus:ring-ring">
            </div>

            <!-- End Date -->
            <div class="relative">
                <span class="absolute left-3 top-2.5 text-xs font-medium text-muted-foreground">Sampai:</span>
                <input type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}"
                       class="w-full rounded-md border border-input bg-background pl-16 pr-3 py-2 text-xs text-foreground shadow-xs focus:outline-none focus:ring-1 focus:ring-ring">
            </div>

            <!-- Category Filter -->
            <div class="w-full min-w-0">
                <select name="category_id" class="tom-select rounded-md border border-input bg-background text-xs shadow-xs" placeholder="Semua Kategori">
                    <option value="all">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ ($filters['category_id'] ?? '') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Buttons: Terapkan & Reset -->
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-md bg-primary py-2 px-3 text-xs font-medium text-primary-foreground shadow-xs hover:bg-primary/90 transition-colors">
                    <i data-lucide="filter" class="h-3.5 w-3.5"></i>
                    <span>Terapkan</span>
                </button>
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-input bg-background py-2 px-3 text-xs font-medium text-muted-foreground hover:bg-accent hover:text-foreground transition-colors" title="Reset Filter">
                    <i data-lucide="rotate-ccw" class="h-3.5 w-3.5"></i>
                    <span>Reset</span>
                </a>
            </div>
        </form>
    </div>

    <!-- 6 Metric Cards Responsive Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3.5">
        <!-- Metric 1: Total Reports -->
        <div class="rounded-xl border border-border bg-card p-4 sm:p-5 text-card-foreground shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2">
                <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">{{ $stats['is_citizen'] ? 'Total Laporan Saya' : 'Total Laporan' }}</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-md bg-muted text-foreground shrink-0">
                    <i data-lucide="file-text" class="h-4 w-4"></i>
                </div>
            </div>
            <div>
                <div class="text-2xl font-bold tracking-tight text-foreground">{{ number_format($stats['metrics']['total_reports']) }}</div>
                <p class="text-[11px] text-muted-foreground mt-1">{{ $stats['is_citizen'] ? 'Semua laporan Anda' : 'Keseluruhan laporan warga' }}</p>
            </div>
        </div>

        <!-- Metric 2: Pending -->
        <div class="rounded-xl border border-border bg-card p-4 sm:p-5 text-card-foreground shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2">
                <span class="text-xs font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wider">Pending</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-md bg-amber-500/10 text-amber-600 dark:text-amber-400 shrink-0">
                    <i data-lucide="clock" class="h-4 w-4"></i>
                </div>
            </div>
            <div>
                <div class="text-2xl font-bold tracking-tight text-amber-600 dark:text-amber-400">{{ number_format($stats['metrics']['total_pending']) }}</div>
                <p class="text-[11px] text-muted-foreground mt-1">Menunggu penanganan</p>
            </div>
        </div>

        <!-- Metric 3: In Progress -->
        <div class="rounded-xl border border-border bg-card p-4 sm:p-5 text-card-foreground shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2">
                <span class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wider">In Progress</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-md bg-blue-500/10 text-blue-600 dark:text-blue-400 shrink-0">
                    <i data-lucide="truck" class="h-4 w-4"></i>
                </div>
            </div>
            <div>
                <div class="text-2xl font-bold tracking-tight text-blue-600 dark:text-blue-400">{{ number_format($stats['metrics']['total_in_progress']) }}</div>
                <p class="text-[11px] text-muted-foreground mt-1">Petugas di lokasi</p>
            </div>
        </div>

        <!-- Metric 4: Completed -->
        <div class="rounded-xl border border-border bg-card p-4 sm:p-5 text-card-foreground shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2">
                <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Completed</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-md bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 shrink-0">
                    <i data-lucide="check-circle" class="h-4 w-4"></i>
                </div>
            </div>
            <div>
                <div class="text-2xl font-bold tracking-tight text-emerald-600 dark:text-emerald-400">{{ number_format($stats['metrics']['total_completed']) }}</div>
                <p class="text-[11px] text-muted-foreground mt-1">Pengangkutan selesai</p>
            </div>
        </div>

        <!-- Metric 5: Rejected -->
        <div class="rounded-xl border border-border bg-card p-4 sm:p-5 text-card-foreground shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2">
                <span class="text-xs font-semibold text-rose-600 dark:text-rose-400 uppercase tracking-wider">Rejected</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-md bg-rose-500/10 text-rose-600 dark:text-rose-400 shrink-0">
                    <i data-lucide="x-circle" class="h-4 w-4"></i>
                </div>
            </div>
            <div>
                <div class="text-2xl font-bold tracking-tight text-rose-600 dark:text-rose-400">{{ number_format($stats['metrics']['total_rejected']) }}</div>
                <p class="text-[11px] text-muted-foreground mt-1">Laporan ditolak</p>
            </div>
        </div>

        <!-- Metric 6: Users / Laporan Selesai -->
        <div class="rounded-xl border border-border bg-card p-4 sm:p-5 text-card-foreground shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2">
                <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">{{ $stats['is_citizen'] ? 'Laporan Ditangani' : 'Total Warga' }}</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-md bg-muted text-foreground shrink-0">
                    <i data-lucide="{{ $stats['is_citizen'] ? 'check-check' : 'users' }}" class="h-4 w-4"></i>
                </div>
            </div>
            <div>
                <div class="text-2xl font-bold tracking-tight text-foreground">{{ number_format($stats['metrics']['total_users']) }}</div>
                <p class="text-[11px] text-muted-foreground mt-1">{{ $stats['is_citizen'] ? 'Laporan berhasil diselesaikan' : 'Akun warga aktif' }}</p>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- 30-Day Trend Line Chart -->
        <div class="lg:col-span-2 rounded-xl border border-border bg-card p-6 shadow-xs flex flex-col">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-border mb-4 gap-2">
                <div>
                    <h3 class="font-semibold tracking-tight text-foreground">Tren Laporan Masuk (30 Hari Terakhir)</h3>
                    <p class="text-xs text-muted-foreground">Volume akumulasi laporan sampah harian dari aplikasi mobile.</p>
                </div>
                <div class="flex items-center gap-1.5 text-xs text-muted-foreground shrink-0">
                    <span class="inline-block h-2.5 w-2.5 rounded-full bg-emerald-600"></span>
                    <span class="font-medium text-foreground">Laporan</span>
                </div>
            </div>
            <div class="h-80 w-full relative">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        <!-- Urgency Distribution Donut Chart & Scale Summary -->
        <div class="rounded-xl border border-border bg-card p-6 shadow-xs flex flex-col justify-between">
            <div>
                <div class="pb-4 border-b border-border mb-4">
                    <h3 class="font-semibold tracking-tight text-foreground">Sebaran Urgensi Laporan</h3>
                    <p class="text-xs text-muted-foreground">Prioritas penanganan laporan berdasarkan tingkat urgensi.</p>
                </div>
                <div class="h-52 w-full flex items-center justify-center relative">
                    <canvas id="urgencyChart"></canvas>
                </div>
            </div>
            
            <div class="mt-4 border-t border-border pt-4">
                <span class="text-[11px] font-semibold text-muted-foreground uppercase tracking-wider block mb-2">Estimasi Volume Sampah</span>
                <div class="grid grid-cols-3 gap-2 text-center text-xs">
                    <div class="p-2 rounded-lg bg-muted/40 border border-border/50">
                        <span class="text-muted-foreground block text-[11px] font-medium">Besar</span>
                        <span class="font-bold text-foreground text-sm mt-0.5 block">{{ $stats['scale_distribution']['large'] }}</span>
                    </div>
                    <div class="p-2 rounded-lg bg-muted/40 border border-border/50">
                        <span class="text-muted-foreground block text-[11px] font-medium">Sedang</span>
                        <span class="font-bold text-foreground text-sm mt-0.5 block">{{ $stats['scale_distribution']['medium'] }}</span>
                    </div>
                    <div class="p-2 rounded-lg bg-muted/40 border border-border/50">
                        <span class="text-muted-foreground block text-[11px] font-medium">Kecil</span>
                        <span class="font-bold text-foreground text-sm mt-0.5 block">{{ $stats['scale_distribution']['small'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Status Logs (Aktivitas Penanganan Sampah) -->
    <div class="mt-6 rounded-xl border border-border bg-card shadow-xs overflow-hidden">
        <div class="flex items-center justify-between p-6 border-b border-border">
            <div>
                <h3 class="font-semibold tracking-tight text-foreground">Aktivitas Penanganan Sampah Terbaru</h3>
                <p class="text-xs text-muted-foreground mt-0.5">Catatan riwayat pembaruan status laporan sampah terkini oleh petugas.</p>
            </div>
            <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-primary hover:underline">
                <span>Lihat Semua Laporan</span>
                <i data-lucide="arrow-right" class="h-3.5 w-3.5"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-muted/50 text-xs uppercase text-muted-foreground border-b border-border">
                    <tr>
                        <th class="px-6 py-3 font-semibold">Laporan</th>
                        <th class="px-6 py-3 font-semibold">Petugas</th>
                        <th class="px-6 py-3 font-semibold">Perubahan Status</th>
                        <th class="px-6 py-3 font-semibold">Catatan Tindakan</th>
                        <th class="px-6 py-3 font-semibold">Waktu</th>
                        <th class="px-6 py-3 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($stats['recent_status_logs'] as $log)
                        <tr class="hover:bg-muted/30 transition-colors">
                            <td class="px-6 py-4 font-mono font-medium text-xs text-foreground">
                                {{ $log->report ? $log->report->tracking_code : '#' . $log->report_id }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-medium text-foreground text-xs">{{ $log->changedByUser->name ?? 'Sistem' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="inline-flex items-center gap-1.5 text-xs">
                                    <span class="rounded bg-muted px-2 py-0.5 font-medium text-muted-foreground">{{ ucfirst($log->from_status ?? 'Baru') }}</span>
                                    <i data-lucide="arrow-right" class="h-3 w-3 text-muted-foreground"></i>
                                    @if ($log->to_status === 'pending')
                                        <span class="rounded bg-amber-500/10 text-amber-700 px-2 py-0.5 font-semibold">Pending</span>
                                    @elseif ($log->to_status === 'in_progress')
                                        <span class="rounded bg-blue-500/10 text-blue-700 px-2 py-0.5 font-semibold">In Progress</span>
                                    @elseif ($log->to_status === 'completed')
                                        <span class="rounded bg-emerald-500/10 text-emerald-700 px-2 py-0.5 font-semibold">Completed</span>
                                    @else
                                        <span class="rounded bg-rose-500/10 text-rose-700 px-2 py-0.5 font-semibold">Rejected</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs text-muted-foreground max-w-xs truncate">
                                {{ $log->notes ?: '-' }}
                            </td>
                            <td class="px-6 py-4 text-xs text-muted-foreground whitespace-nowrap">
                                {{ $log->created_at ? $log->created_at->diffForHumans() : '-' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if ($log->report)
                                    <a href="{{ route('admin.reports.index', ['search' => $log->report->tracking_code]) }}"
                                       class="inline-flex items-center gap-1 rounded-md border border-input bg-background px-2.5 py-1 text-xs font-medium shadow-xs hover:bg-accent transition-colors">
                                        <i data-lucide="external-link" class="h-3 w-3"></i>
                                        <span>Buka</span>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-sm text-muted-foreground">
                                <i data-lucide="inbox" class="h-7 w-7 mx-auto mb-2 opacity-40"></i>
                                Belum ada riwayat aktivitas penanganan laporan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Line Chart
        const trendCanvas = document.getElementById('trendChart');
        if (trendCanvas) {
            const trendCtx = trendCanvas.getContext('2d');
            const trendLabels = @json($stats['trend_30_days']['labels']);
            const trendValues = @json($stats['trend_30_days']['values']);

            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: trendLabels.map(l => l.substring(5)), // MM-DD
                    datasets: [{
                        label: 'Jumlah Laporan',
                        data: trendValues,
                        borderColor: '#16a34a',
                        backgroundColor: 'rgba(22, 163, 74, 0.1)',
                        borderWidth: 2.5,
                        tension: 0.35,
                        fill: true,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#16a34a'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0, font: { size: 11 } },
                            grid: { color: 'rgba(0, 0, 0, 0.05)' }
                        },
                        x: {
                            ticks: { font: { size: 10 } },
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        // Urgency Donut Chart
        const urgencyCanvas = document.getElementById('urgencyChart');
        if (urgencyCanvas) {
            const urgencyCtx = urgencyCanvas.getContext('2d');
            const urgencyLabels = @json($stats['urgency_distribution']['labels']);
            const urgencyValues = @json($stats['urgency_distribution']['values']);
            const urgencyColors = @json($stats['urgency_distribution']['colors']);

            new Chart(urgencyCtx, {
                type: 'doughnut',
                data: {
                    labels: urgencyLabels,
                    datasets: [{
                        data: urgencyValues,
                        backgroundColor: urgencyColors,
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 12, font: { size: 11 }, padding: 14 }
                        }
                    },
                    cutout: '70%'
                }
            });
        }
    });
</script>
@endpush
@endsection
