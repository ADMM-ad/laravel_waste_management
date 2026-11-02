@extends('layouts.master')

@section('title', 'Laporan Pengelolaan Sampah')

@section('content')
<div class="space-y-6">
    <!-- Page Header (Shadcn Header) -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-foreground">Laporan Sampah</h1>
            <p class="text-sm text-muted-foreground">Rekapitulasi volume, kategori, dan status pengolahan sampah.</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" class="inline-flex items-center justify-center gap-2 rounded-md border border-input bg-background px-3 py-2 text-sm font-medium shadow-xs hover:bg-accent hover:text-accent-foreground transition-colors">
                <i data-lucide="download" class="h-4 w-4"></i>
                <span>Ekspor CSV</span>
            </button>
            <button type="button" onclick="window.print()" class="inline-flex items-center justify-center gap-2 rounded-md bg-primary px-3 py-2 text-sm font-medium text-primary-foreground shadow-xs hover:bg-primary/90 transition-colors">
                <i data-lucide="printer" class="h-4 w-4"></i>
                <span>Cetak Laporan</span>
            </button>
        </div>
    </div>

    <!-- Stat Metric Cards (Shadcn Card Component) -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <!-- Card 1 -->
        <div class="rounded-xl border border-border bg-card p-6 text-card-foreground shadow-xs">
            <div class="flex items-center justify-between space-y-0 pb-2">
                <span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Total Sampah Terkumpul</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-md bg-muted text-foreground">
                    <i data-lucide="scale" class="h-4 w-4"></i>
                </div>
            </div>
            <div class="space-y-1">
                <div class="text-2xl font-bold tracking-tight">1.450 <span class="text-sm font-normal text-muted-foreground">Kg</span></div>
                <p class="text-xs text-muted-foreground flex items-center gap-1">
                    <span class="text-emerald-600 dark:text-emerald-400 font-medium">+12.5%</span> dari bulan lalu
                </p>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="rounded-xl border border-border bg-card p-6 text-card-foreground shadow-xs">
            <div class="flex items-center justify-between space-y-0 pb-2">
                <span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Didaur Ulang / Terpilah</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-md bg-muted text-foreground">
                    <i data-lucide="recycle" class="h-4 w-4"></i>
                </div>
            </div>
            <div class="space-y-1">
                <div class="text-2xl font-bold tracking-tight">920 <span class="text-sm font-normal text-muted-foreground">Kg</span></div>
                <p class="text-xs text-muted-foreground flex items-center gap-1">
                    <span class="text-emerald-600 dark:text-emerald-400 font-medium">63.4%</span> dari total sampah
                </p>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="rounded-xl border border-border bg-card p-6 text-card-foreground shadow-xs">
            <div class="flex items-center justify-between space-y-0 pb-2">
                <span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Residu (Ke TPA)</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-md bg-muted text-foreground">
                    <i data-lucide="truck" class="h-4 w-4"></i>
                </div>
            </div>
            <div class="space-y-1">
                <div class="text-2xl font-bold tracking-tight">530 <span class="text-sm font-normal text-muted-foreground">Kg</span></div>
                <p class="text-xs text-muted-foreground flex items-center gap-1">
                    <span class="text-amber-600 dark:text-amber-400 font-medium">36.6%</span> residu tak terurai
                </p>
            </div>
        </div>
    </div>

    <!-- Data Table (Shadcn Table Component) -->
    <div class="rounded-xl border border-border bg-card text-card-foreground shadow-xs overflow-hidden">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-border p-4 sm:px-6">
            <div>
                <h3 class="font-semibold leading-none tracking-tight">Rincian Pengangkutan Terbaru</h3>
                <p class="text-xs text-muted-foreground mt-1">Data pencatatan armada pengangkutan sampah di wilayah operasional.</p>
            </div>
            <span class="inline-flex items-center rounded-md bg-secondary px-2 py-1 text-xs font-medium text-secondary-foreground border border-border">
                Sampel Data
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-border bg-muted/40 text-xs font-medium text-muted-foreground uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3">No</th>
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3">Lokasi TPS</th>
                        <th class="px-6 py-3">Kategori</th>
                        <th class="px-6 py-3">Volume</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Petugas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <tr class="hover:bg-muted/50 transition-colors">
                        <td class="px-6 py-3.5 font-medium text-foreground">1</td>
                        <td class="px-6 py-3.5 text-muted-foreground">02 Nov 2026</td>
                        <td class="px-6 py-3.5 font-medium text-foreground">TPS Pasar Induk</td>
                        <td class="px-6 py-3.5">
                            <span class="inline-flex items-center rounded-md bg-emerald-500/10 px-2 py-0.5 text-xs font-medium text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                Organik
                            </span>
                        </td>
                        <td class="px-6 py-3.5 font-semibold text-foreground">320 Kg</td>
                        <td class="px-6 py-3.5">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/10 px-2.5 py-0.5 text-xs font-medium text-emerald-600 dark:text-emerald-400">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                Selesai
                            </span>
                        </td>
                        <td class="px-6 py-3.5 text-muted-foreground">Ahmad Fauzi</td>
                    </tr>
                    <tr class="hover:bg-muted/50 transition-colors">
                        <td class="px-6 py-3.5 font-medium text-foreground">2</td>
                        <td class="px-6 py-3.5 text-muted-foreground">02 Nov 2026</td>
                        <td class="px-6 py-3.5 font-medium text-foreground">TPS RW 04 Melati</td>
                        <td class="px-6 py-3.5">
                            <span class="inline-flex items-center rounded-md bg-sky-500/10 px-2 py-0.5 text-xs font-medium text-sky-600 dark:text-sky-400 border border-sky-500/20">
                                Plastik & Kertas
                            </span>
                        </td>
                        <td class="px-6 py-3.5 font-semibold text-foreground">180 Kg</td>
                        <td class="px-6 py-3.5">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-sky-500/10 px-2.5 py-0.5 text-xs font-medium text-sky-600 dark:text-sky-400">
                                <span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>
                                Bank Sampah
                            </span>
                        </td>
                        <td class="px-6 py-3.5 text-muted-foreground">Budi Santoso</td>
                    </tr>
                    <tr class="hover:bg-muted/50 transition-colors">
                        <td class="px-6 py-3.5 font-medium text-foreground">3</td>
                        <td class="px-6 py-3.5 text-muted-foreground">01 Nov 2026</td>
                        <td class="px-6 py-3.5 font-medium text-foreground">TPS Komersial Barat</td>
                        <td class="px-6 py-3.5">
                            <span class="inline-flex items-center rounded-md bg-amber-500/10 px-2 py-0.5 text-xs font-medium text-amber-600 dark:text-amber-400 border border-amber-500/20">
                                Residu Campur
                            </span>
                        </td>
                        <td class="px-6 py-3.5 font-semibold text-foreground">450 Kg</td>
                        <td class="px-6 py-3.5">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-500/10 px-2.5 py-0.5 text-xs font-medium text-amber-600 dark:text-amber-400">
                                <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                Ke TPA
                            </span>
                        </td>
                        <td class="px-6 py-3.5 text-muted-foreground">Siti Rahma</td>
                    </tr>
                    <tr class="hover:bg-muted/50 transition-colors">
                        <td class="px-6 py-3.5 font-medium text-foreground">4</td>
                        <td class="px-6 py-3.5 text-muted-foreground">01 Nov 2026</td>
                        <td class="px-6 py-3.5 font-medium text-foreground">TPS Perumahan Asri</td>
                        <td class="px-6 py-3.5">
                            <span class="inline-flex items-center rounded-md bg-emerald-500/10 px-2 py-0.5 text-xs font-medium text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                Organik
                            </span>
                        </td>
                        <td class="px-6 py-3.5 font-semibold text-foreground">210 Kg</td>
                        <td class="px-6 py-3.5">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/10 px-2.5 py-0.5 text-xs font-medium text-emerald-600 dark:text-emerald-400">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                Selesai
                            </span>
                        </td>
                        <td class="px-6 py-3.5 text-muted-foreground">Ahmad Fauzi</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Table Footer Pagination Info -->
        <div class="flex items-center justify-between border-t border-border px-6 py-3 text-xs text-muted-foreground">
            <div>
                Menampilkan <span class="font-medium text-foreground">4</span> data sampel
            </div>
            <div class="flex items-center gap-1">
                <button type="button" class="h-8 rounded-md border border-input px-2 text-xs font-medium opacity-50 cursor-not-allowed">Sebelumnya</button>
                <button type="button" class="h-8 rounded-md border border-input px-2 text-xs font-medium opacity-50 cursor-not-allowed">Selanjutnya</button>
            </div>
        </div>
    </div>
</div>
@endsection
