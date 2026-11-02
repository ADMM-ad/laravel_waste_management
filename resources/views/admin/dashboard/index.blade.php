@extends('layouts.master')

@section('title', 'Dashboard Ikhtisar')

@section('content')
<div class="space-y-6">
    <!-- Page Header & Filter Form -->
    <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-foreground">Dashboard Operasional</h1>
            <p class="text-sm text-muted-foreground">Pemantauan metrik laporan sampah, tren eskalasi, dan distribusi kategori secara real-time.</p>
        </div>

        <!-- Filter Bar -->
        <form method="GET" action="{{ route('admin.dashboard') }}" class="flex flex-wrap items-center gap-2">
            <div class="flex items-center gap-1.5 rounded-md border border-input bg-card px-2.5 py-1.5 text-xs shadow-xs">
                <span class="text-muted-foreground font-medium">Dari:</span>
                <input type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}"
                       class="bg-transparent text-foreground focus:outline-none cursor-pointer">
            </div>
            <div class="flex items-center gap-1.5 rounded-md border border-input bg-card px-2.5 py-1.5 text-xs shadow-xs">
                <span class="text-muted-foreground font-medium">Sampai:</span>
                <input type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}"
                       class="bg-transparent text-foreground focus:outline-none cursor-pointer">
            </div>
            <select name="category_id" class="rounded-md border border-input bg-card px-2.5 py-1.5 text-xs text-foreground shadow-xs focus:outline-none">
                <option value="all">Semua Kategori</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" {{ ($filters['category_id'] ?? '') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="inline-flex items-center gap-1.5 rounded-md bg-primary px-3 py-1.5 text-xs font-semibold text-primary-foreground shadow-xs hover:bg-primary/90 transition-colors">
                <i data-lucide="filter" class="h-3.5 w-3.5"></i>
                <span>Terapkan</span>
            </button>
            @if (!empty($filters['start_date']) || !empty($filters['end_date']) || (!empty($filters['category_id']) && $filters['category_id'] !== 'all'))
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1 rounded-md border border-input bg-background px-2.5 py-1.5 text-xs text-muted-foreground hover:bg-accent transition-colors">
                    <i data-lucide="rotate-ccw" class="h-3 w-3"></i>
                    <span>Reset</span>
                </a>
            @endif
        </form>
    </div>

    <!-- 6 Metric Cards Responsive Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4">
        <!-- Metric 1: Total Reports -->
        <div class="rounded-xl border border-border bg-card p-5 text-card-foreground shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2">
                <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Total Laporan</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-md bg-muted text-foreground shrink-0">
                    <i data-lucide="file-text" class="h-4 w-4"></i>
                </div>
            </div>
            <div>
                <div class="text-2xl font-bold tracking-tight text-foreground">{{ number_format($stats['metrics']['total_reports']) }}</div>
                <p class="text-[11px] text-muted-foreground mt-1">Keseluruhan laporan warga</p>
            </div>
        </div>

        <!-- Metric 2: Pending -->
        <div class="rounded-xl border border-border bg-card p-5 text-card-foreground shadow-xs flex flex-col justify-between">
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
        <div class="rounded-xl border border-border bg-card p-5 text-card-foreground shadow-xs flex flex-col justify-between">
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
        <div class="rounded-xl border border-border bg-card p-5 text-card-foreground shadow-xs flex flex-col justify-between">
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
        <div class="rounded-xl border border-border bg-card p-5 text-card-foreground shadow-xs flex flex-col justify-between">
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

        <!-- Metric 6: Users -->
        <div class="rounded-xl border border-border bg-card p-5 text-card-foreground shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2">
                <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Total Warga</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-md bg-muted text-foreground shrink-0">
                    <i data-lucide="users" class="h-4 w-4"></i>
                </div>
            </div>
            <div>
                <div class="text-2xl font-bold tracking-tight text-foreground">{{ number_format($stats['metrics']['total_users']) }}</div>
                <p class="text-[11px] text-muted-foreground mt-1">Akun warga aktif</p>
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

        <!-- Category Distribution Donut Chart -->
        <div class="rounded-xl border border-border bg-card p-6 shadow-xs flex flex-col justify-between">
            <div>
                <div class="pb-4 border-b border-border mb-4">
                    <h3 class="font-semibold tracking-tight text-foreground">Distribusi Jenis Sampah</h3>
                    <p class="text-xs text-muted-foreground">Proporsi kategori sampah berdasarkan AI & kurasi.</p>
                </div>
                <div class="h-60 w-full flex items-center justify-center relative">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
            <div class="mt-4 grid grid-cols-3 gap-2 text-center text-xs border-t border-border pt-4">
                @foreach ($stats['category_distribution'] as $cat)
                    <div class="p-1.5 rounded-lg bg-muted/40">
                        <span class="text-muted-foreground block text-[11px] font-medium truncate">{{ $cat['name'] }}</span>
                        <span class="font-bold text-foreground text-sm mt-0.5 block">{{ $cat['count'] }}</span>
                    </div>
                @endforeach
            </div>
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

        // Donut Chart
        const catCanvas = document.getElementById('categoryChart');
        if (catCanvas) {
            const catCtx = catCanvas.getContext('2d');
            const catLabels = @json($stats['category_distribution']->pluck('name'));
            const catCounts = @json($stats['category_distribution']->pluck('count'));

            new Chart(catCtx, {
                type: 'doughnut',
                data: {
                    labels: catLabels,
                    datasets: [{
                        data: catCounts,
                        backgroundColor: ['#16a34a', '#2563eb', '#f59e0b'],
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
