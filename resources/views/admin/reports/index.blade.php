@extends('layouts.master')

@section('title', 'Manajemen Laporan Sampah')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-foreground">{{ Auth::user()->hasRole('user') ? 'Laporan Sampah Saya' : 'Daftar Laporan Sampah' }}</h1>
            <p class="text-sm text-muted-foreground">{{ Auth::user()->hasRole('user') ? 'Pantau perkembangan dan status penanganan laporan sampah yang Anda ajukan.' : 'Kelola laporan masyarakat, pantau deteksi AI, dan perbarui status penanganan.' }}</p>
        </div>
        @if (Auth::user()->hasRole('user'))
            <a href="{{ route('admin.reports.create') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-primary-foreground shadow-xs hover:bg-primary/90 transition-colors">
                <i data-lucide="plus" class="h-4 w-4"></i>
                <span>Tambah Laporan Baru</span>
            </a>
        @endif
    </div>

    <!-- Filters & Search Bar (Shadcn Filter Toolbar) -->
    <div class="rounded-xl border border-border bg-card p-4 shadow-xs">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5 items-center">
            <!-- Search Input -->
            <div class="relative sm:col-span-2">
                <i data-lucide="search" class="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground"></i>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari tracking code atau alamat..."
                       class="w-full rounded-md border border-input bg-background pl-9 pr-3 py-2 text-xs shadow-xs placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-ring">
            </div>

            <!-- Category Filter (Searchable TomSelect) -->
            <div class="w-full min-w-0">
                <select name="category_id" class="tom-select rounded-md border border-input bg-background text-xs shadow-xs" placeholder="Cari Kategori...">
                    <option value="all">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ ($filters['category_id'] ?? '') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter (Searchable TomSelect) -->
            <div class="w-full min-w-0">
                <select name="status" class="tom-select rounded-md border border-input bg-background text-xs shadow-xs" placeholder="Cari Status...">
                    <option value="all">Semua Status</option>
                    <option value="pending" {{ ($filters['status'] ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_progress" {{ ($filters['status'] ?? '') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ ($filters['status'] ?? '') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="rejected" {{ ($filters['status'] ?? '') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-md bg-primary py-2 px-3 text-xs font-medium text-primary-foreground shadow-xs hover:bg-primary/90 transition-colors">
                    <i data-lucide="filter" class="h-3.5 w-3.5"></i>
                    <span>Filter</span>
                </button>
                <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-input bg-background py-2 px-3 text-xs font-medium text-muted-foreground hover:bg-accent hover:text-foreground transition-colors" title="Bersihkan Filter">
                    <i data-lucide="rotate-ccw" class="h-3.5 w-3.5"></i>
                    <span>Reset</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Reports Table (Shadcn Table) -->
    <div class="rounded-xl border border-border bg-card shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-border bg-muted/40 text-xs font-medium text-muted-foreground uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3.5">Tracking Code</th>
                        <th class="px-5 py-3.5">Tanggal</th>
                        <th class="px-5 py-3.5">Pelapor</th>
                        <th class="px-5 py-3.5">Kategori</th>
                        <th class="px-5 py-3.5">Urgensi</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($reports as $report)
                        <tr class="hover:bg-muted/30 transition-colors">
                            <td class="px-5 py-4 font-mono font-medium text-xs text-foreground">
                                {{ $report->tracking_code }}
                            </td>
                            <td class="px-5 py-4 text-xs text-muted-foreground">
                                {{ $report->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-medium text-xs text-foreground">{{ $report->user->name ?? 'Anonim' }}</div>
                                <div class="text-[11px] text-muted-foreground">{{ $report->user->email ?? '-' }}</div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center rounded-md bg-secondary px-2 py-0.5 text-xs font-medium text-secondary-foreground border border-border">
                                    {{ $report->category->name ?? 'Uncategorized' }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                @if ($report->urgency === 'high')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-rose-500/10 px-2.5 py-0.5 text-[11px] font-semibold text-rose-600 dark:text-rose-400">
                                        <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span> Tinggi
                                    </span>
                                @elseif ($report->urgency === 'medium')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-500/10 px-2.5 py-0.5 text-[11px] font-semibold text-amber-600 dark:text-amber-400">
                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span> Sedang
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-500/10 px-2.5 py-0.5 text-[11px] font-semibold text-muted-foreground">
                                        <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span> Rendah
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @if ($report->status === 'pending')
                                    <span class="inline-flex items-center rounded-md bg-amber-500/10 border border-amber-500/20 px-2 py-1 text-xs font-medium text-amber-700 dark:text-amber-400">
                                        Pending
                                    </span>
                                @elseif ($report->status === 'in_progress')
                                    <span class="inline-flex items-center rounded-md bg-blue-500/10 border border-blue-500/20 px-2 py-1 text-xs font-medium text-blue-700 dark:text-blue-400">
                                        In Progress
                                    </span>
                                @elseif ($report->status === 'completed')
                                    <span class="inline-flex items-center rounded-md bg-emerald-500/10 border border-emerald-500/20 px-2 py-1 text-xs font-medium text-emerald-700 dark:text-emerald-400">
                                        Completed
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-md bg-rose-500/10 border border-rose-500/20 px-2 py-1 text-xs font-medium text-rose-700 dark:text-rose-400">
                                        Rejected
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" onclick="openDetailModal({{ $report->id }})"
                                            class="inline-flex items-center gap-1.5 rounded-md border border-input bg-background px-2.5 py-1.5 text-xs font-medium shadow-xs hover:bg-accent hover:text-accent-foreground transition-colors"
                                            title="Lihat Detail Laporan">
                                        <i data-lucide="eye" class="h-3.5 w-3.5"></i>
                                        <span>Detail</span>
                                    </button>

                                    @if (Auth::user()->hasRole('admin'))
                                        <button type="button"
                                                onclick="openStatusUpdateShortcutModal({{ $report->id }}, '{{ $report->status }}', '{{ $report->tracking_code }}')"
                                                class="inline-flex items-center gap-1.5 rounded-md border border-emerald-600/30 bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 px-2.5 py-1.5 text-xs font-medium shadow-xs hover:bg-emerald-100 transition-colors"
                                                title="Ubah Status">
                                            <i data-lucide="refresh-cw" class="h-3.5 w-3.5"></i>
                                            <span>Ubah Status</span>
                                        </button>

                                        <button type="button"
                                                onclick="openDeleteConfirmationModal('{{ route('admin.reports.destroy', $report->id) }}', 'Hapus Laporan {{ $report->tracking_code }}', 'Apakah Anda yakin ingin menghapus data laporan ini? Data laporan akan diarsipkan dan tidak lagi muncul di daftar aktif.')"
                                                class="inline-flex items-center gap-1 rounded-md border border-destructive/30 bg-destructive/10 px-2.5 py-1.5 text-xs font-medium text-destructive hover:bg-destructive hover:text-destructive-foreground transition-colors"
                                                title="Hapus Laporan">
                                            <i data-lucide="trash-2" class="h-3.5 w-3.5"></i>
                                            <span>Hapus</span>
                                        </button>
                                    @else
                                        @if ($report->status === 'pending')
                                            <a href="{{ route('admin.reports.edit', $report->id) }}"
                                               class="inline-flex items-center gap-1.5 rounded-md border border-input bg-background px-2.5 py-1.5 text-xs font-medium shadow-xs hover:bg-accent transition-colors"
                                               title="Edit Laporan Anda">
                                                <i data-lucide="edit-3" class="h-3.5 w-3.5"></i>
                                                <span>Edit</span>
                                            </a>

                                            <button type="button"
                                                    onclick="openDeleteConfirmationModal('{{ route('admin.reports.destroy', $report->id) }}', 'Batalkan Laporan {{ $report->tracking_code }}', 'Apakah Anda yakin ingin membatalkan dan menghapus laporan yang Anda buat ini?')"
                                                    class="inline-flex items-center gap-1 rounded-md border border-destructive/30 bg-destructive/10 px-2.5 py-1.5 text-xs font-medium text-destructive hover:bg-destructive hover:text-destructive-foreground transition-colors"
                                                    title="Hapus Laporan">
                                                <i data-lucide="trash-2" class="h-3.5 w-3.5"></i>
                                                <span>Hapus</span>
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-sm text-muted-foreground">
                                <i data-lucide="inbox" class="h-8 w-8 mx-auto mb-2 opacity-50"></i>
                                Belum ada data laporan sampah yang sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($reports->hasPages())
            <div class="border-t border-border p-4">
                {{ $reports->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Detail & Status Update Modal (Shadcn Dialog) -->
<div id="detailModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
    <div class="w-full max-w-2xl rounded-2xl border border-border bg-card p-4 sm:p-6 text-card-foreground shadow-2xl my-4 sm:my-8 max-h-[85vh] sm:max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-border pb-3 mb-4">
            <div>
                <h3 class="font-semibold text-lg" id="modalTrackingCode">Detail Laporan</h3>
                <p class="text-xs text-muted-foreground" id="modalCreatedAt">-</p>
            </div>
            <button type="button" onclick="closeDetailModal()" class="inline-flex h-8 w-8 items-center justify-center rounded-md text-muted-foreground hover:bg-accent">
                <i data-lucide="x" class="h-4 w-4"></i>
            </button>
        </div>

        <!-- Modal Body Content -->
        <div class="space-y-5" id="modalBody">
            <!-- Loading state -->
            <div id="modalLoading" class="py-12 text-center text-muted-foreground text-sm">
                Memuat data detail laporan...
            </div>

            <!-- Content state -->
            <div id="modalContent" class="hidden space-y-5">
                <!-- Photo & AI Prediction Grid -->
                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="rounded-lg border border-border overflow-hidden bg-muted flex items-center justify-center">
                        <img id="modalPhoto" src="" alt="Foto Sampah" class="w-full h-52 object-cover">
                    </div>
                    <div class="space-y-3">
                        <div class="rounded-lg border border-border p-3.5 bg-muted/30">
                            <span class="text-[11px] font-medium text-muted-foreground uppercase tracking-wider block">Prediksi AI Model</span>
                            <div class="flex items-center justify-between mt-1">
                                <span class="font-semibold text-foreground text-sm" id="modalAiClass">-</span>
                                <span class="rounded bg-primary/10 text-primary px-2 py-0.5 text-xs font-semibold" id="modalAiConfidence">-</span>
                            </div>
                        </div>

                        <div class="rounded-lg border border-border p-3.5 bg-muted/30">
                            <span class="text-[11px] font-medium text-muted-foreground uppercase tracking-wider block">Kategori Terpilih</span>
                            <span class="font-medium text-foreground text-sm block mt-1" id="modalCategory">-</span>
                            <div class="flex items-center gap-2 mt-2 text-xs text-muted-foreground">
                                <span>Skala: <strong class="text-foreground" id="modalScale">-</strong></span>
                                <span>•</span>
                                <span>Urgensi: <strong class="text-foreground" id="modalUrgency">-</strong></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Location & Description -->
                <div class="space-y-2">
                    <span class="text-xs font-semibold text-foreground">Deskripsi Warga:</span>
                    <p class="text-xs text-muted-foreground bg-muted/30 p-3 rounded-lg border border-border" id="modalDescription"></p>
                </div>

                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-foreground">Lokasi Pelaporan:</span>
                        <a id="modalMapsLink" href="#" target="_blank" class="inline-flex items-center gap-1 text-xs text-primary hover:underline">
                            <i data-lucide="map-pin" class="h-3.5 w-3.5"></i>
                            <span>Buka di Google Maps</span>
                        </a>
                    </div>
                    <p class="text-xs text-muted-foreground" id="modalAddress">-</p>
                </div>

                <!-- Audit Trail Logs Timeline -->
                <div class="space-y-2 border-t border-border pt-4">
                    <span class="text-xs font-semibold text-foreground">Riwayat Perjalanan Status (Audit Trail):</span>
                    <div class="space-y-2" id="modalLogsContainer">
                        <!-- Dynamically populated -->
                    </div>
                </div>

                @if (Auth::user()->hasRole('admin'))
                    <!-- Status Update Form (Admin Only) -->
                    <form id="statusUpdateForm" method="POST" action="" class="border-t border-border pt-4 space-y-3">
                        @csrf
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-foreground">Perbarui Status Penanganan:</span>
                        </div>

                        <div class="grid sm:grid-cols-2 gap-3 items-center" id="formInputsContainer">
                            <div>
                                <select id="statusSelect" name="status" onchange="handleStatusChange(this.value)"
                                        class="tom-select w-full rounded-md border border-input bg-background text-xs shadow-xs" placeholder="Cari status...">
                                    <option value="pending">Pending - Menunggu Verifikasi</option>
                                    <option value="in_progress">In Progress - Dalam Penanganan</option>
                                    <option value="completed">Completed - Selesai</option>
                                    <option value="rejected">Rejected - Ditolak</option>
                                </select>
                            </div>
                            <div>
                                <button type="submit" id="btnUpdateStatus" class="w-full inline-flex items-center justify-center gap-1.5 rounded-md bg-primary py-2 px-4 text-xs font-medium text-primary-foreground shadow-xs hover:bg-primary/90 transition-colors">
                                    <i data-lucide="save" class="h-3.5 w-3.5"></i>
                                    <span>Simpan Status</span>
                                </button>
                            </div>
                        </div>

                        <!-- Rejection note textarea (required if rejected) -->
                        <div id="rejectionNoteField" class="hidden space-y-1">
                            <label for="modalNotes" class="text-xs font-medium text-destructive">Alasan Penolakan Laporan (Wajib diisi min. 5 karakter):</label>
                            <textarea id="modalNotes" name="notes" rows="2" placeholder="Tuliskan alasan spesifik mengapa laporan ditolak..."
                                      class="w-full rounded-md border border-destructive/40 bg-background p-2.5 text-xs shadow-xs placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-destructive"></textarea>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Dedicated Shortcut Status Update Modal (Shadcn Dialog Style) -->
<div id="statusShortcutModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
    <div class="w-full max-w-lg rounded-2xl border border-border bg-card p-5 sm:p-6 text-card-foreground shadow-2xl my-6">
        <div class="flex items-center justify-between border-b border-border pb-3 mb-4">
            <div>
                <h3 class="font-bold text-base text-foreground flex items-center gap-2">
                    <i data-lucide="refresh-cw" class="h-4 w-4 text-emerald-600"></i>
                    <span>Ubah Status Penanganan</span>
                </h3>
                <p class="text-xs text-muted-foreground mt-0.5">Kode Laporan: <span id="shortcutModalTrackingCode" class="font-mono font-semibold text-foreground">-</span></p>
            </div>
            <button type="button" onclick="closeStatusUpdateShortcutModal()" class="inline-flex h-8 w-8 items-center justify-center rounded-md text-muted-foreground hover:bg-accent">
                <i data-lucide="x" class="h-4 w-4"></i>
            </button>
        </div>

        <form id="shortcutStatusForm" method="POST" action="" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-foreground mb-1.5">Pilih Status Baru <span class="text-destructive">*</span></label>
                <select id="shortcutStatusSelect" name="status" class="tom-select w-full" placeholder="Cari status penanganan..." required>
                    <option value="pending">Pending - Menunggu Verifikasi (Laporan baru masuk dari warga)</option>
                    <option value="in_progress">In Progress - Dalam Penanganan (Petugas sedang menuju lokasi / membersihkan)</option>
                    <option value="completed">Completed - Selesai (Timbunan sampah tuntas dibersihkan)</option>
                    <option value="rejected">Rejected - Ditolak (Laporan fiktif / tidak valid / duplikat)</option>
                </select>
            </div>

            <!-- Status explanation helper card -->
            <div class="rounded-lg bg-muted/40 p-3 text-[11px] text-muted-foreground space-y-1">
                <div class="font-semibold text-foreground">Keterangan Maksud Status:</div>
                <div>&bull; <strong>Pending:</strong> Menunggu peninjauan awal dan verifikasi oleh admin/petugas.</div>
                <div>&bull; <strong>In Progress:</strong> Petugas telah ditugaskan dan proses pembersihan sedang berjalan.</div>
                <div>&bull; <strong>Completed:</strong> Pembersihan tuntas dan lokasi telah bersih.</div>
                <div>&bull; <strong>Rejected:</strong> Laporan ditolak (wajib melampirkan alasan penolakan min. 5 karakter).</div>
            </div>

            <!-- Notes textarea -->
            <div id="shortcutNotesField" class="space-y-1">
                <label for="shortcutNotes" id="shortcutNotesLabel" class="text-xs font-semibold text-foreground">Catatan / Tindak Lanjut Petugas:</label>
                <textarea id="shortcutNotes" name="notes" rows="3" placeholder="Tuliskan catatan tindak lanjut petugas (wajib jika status ditolak)..."
                          class="w-full rounded-md border border-input bg-background p-2.5 text-xs shadow-xs placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-ring"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-border">
                <button type="button" onclick="closeStatusUpdateShortcutModal()"
                        class="rounded-lg border border-input bg-background px-4 py-2 text-xs font-medium text-foreground shadow-xs hover:bg-accent transition-colors">
                    Batal
                </button>
                <button type="submit" id="btnSubmitShortcutStatus"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-primary-foreground shadow-xs hover:bg-primary/90 transition-colors">
                    <i data-lucide="check" class="h-3.5 w-3.5"></i>
                    <span>Simpan Perubahan Status</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Custom Delete Confirmation Modal (Shadcn Alert Dialog Style) -->
<div id="deleteConfirmModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-xs transition-opacity" onclick="closeDeleteConfirmationModal()"></div>
    <div class="relative w-full max-w-md rounded-2xl border border-border bg-card p-6 shadow-2xl text-card-foreground">
        <div class="flex items-start gap-4">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-destructive/10 text-destructive">
                <i data-lucide="alert-triangle" class="h-6 w-6"></i>
            </div>
            <div class="flex-1 space-y-1">
                <h3 class="text-base font-bold text-foreground tracking-tight" id="deleteModalTitle">Konfirmasi Hapus</h3>
                <p class="text-xs text-muted-foreground leading-relaxed" id="deleteModalMessage">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
        </div>

        <form id="deleteConfirmForm" method="POST" action="" class="mt-6 flex items-center justify-end gap-2.5">
            @csrf
            @method('DELETE')
            <button type="button" onclick="closeDeleteConfirmationModal()"
                    class="rounded-lg border border-input bg-background px-4 py-2 text-xs font-medium text-foreground shadow-xs hover:bg-accent transition-colors">
                Batal
            </button>
            <button type="submit"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-destructive px-4 py-2 text-xs font-semibold text-destructive-foreground shadow-xs hover:bg-destructive/90 transition-colors">
                <i data-lucide="trash-2" class="h-3.5 w-3.5"></i>
                <span>Ya, Hapus Data</span>
            </button>
        </form>
    </div>
</div>

<script>
    function openDeleteConfirmationModal(actionUrl, title, message) {
        const modal = document.getElementById('deleteConfirmModal');
        const form = document.getElementById('deleteConfirmForm');
        const titleEl = document.getElementById('deleteModalTitle');
        const msgEl = document.getElementById('deleteModalMessage');

        form.action = actionUrl;
        titleEl.innerText = title;
        msgEl.innerText = message;

        modal.classList.remove('hidden');
        lucide.createIcons();
    }

    function closeDeleteConfirmationModal() {
        document.getElementById('deleteConfirmModal').classList.add('hidden');
    }

    function openDetailModal(id) {
        const modal = document.getElementById('detailModal');
        const loading = document.getElementById('modalLoading');
        const content = document.getElementById('modalContent');
        
        modal.classList.remove('hidden');
        loading.classList.remove('hidden');
        content.classList.add('hidden');

        fetch(`{{ url('/admin/reports') }}/${id}`)
            .then(res => res.json())
            .then(response => {
                if (!response.success) {
                    alert('Gagal mengambil data: ' + response.message);
                    closeDetailModal();
                    return;
                }

                const r = response.data;
                document.getElementById('modalTrackingCode').innerText = r.tracking_code;
                document.getElementById('modalCreatedAt').innerText = 'Dilaporkan pada: ' + new Date(r.created_at).toLocaleString('id-ID');
                document.getElementById('modalPhoto').src = r.photo_url || '';
                document.getElementById('modalAiClass').innerText = r.ai_predicted_category || 'Tidak Terdeteksi';
                document.getElementById('modalAiConfidence').innerText = r.ai_confidence_score ? (r.ai_confidence_score * 100).toFixed(1) + '%' : '-';
                document.getElementById('modalCategory').innerText = r.category ? r.category.name : '-';
                document.getElementById('modalScale').innerText = r.scale;
                document.getElementById('modalUrgency').innerText = r.urgency;
                document.getElementById('modalDescription').innerText = r.description;
                document.getElementById('modalAddress').innerText = r.address;
                document.getElementById('modalMapsLink').href = `https://www.google.com/maps?q=${r.latitude},${r.longitude}`;

                // Populate status logs
                const logsContainer = document.getElementById('modalLogsContainer');
                logsContainer.innerHTML = '';
                if (r.status_logs && r.status_logs.length > 0) {
                    r.status_logs.forEach(log => {
                        const div = document.createElement('div');
                        div.className = 'text-xs border-l-2 border-border pl-3 py-1 space-y-0.5';
                        div.innerHTML = `
                            <div class="flex items-center gap-2">
                                <span class="font-semibold uppercase tracking-wider text-[10px] text-muted-foreground">${log.from_status} &rarr; ${log.to_status}</span>
                                <span class="text-[10px] text-muted-foreground">• ${new Date(log.created_at).toLocaleString('id-ID')}</span>
                            </div>
                            <p class="text-muted-foreground">${log.notes || '-'}</p>
                        `;
                        logsContainer.appendChild(div);
                    });
                } else {
                    logsContainer.innerHTML = '<span class="text-xs text-muted-foreground">Belum ada riwayat status.</span>';
                }

                // Status form setup (Admin only)
                const form = document.getElementById('statusUpdateForm');
                if (form) {
                    form.action = `{{ url('/admin/reports') }}/${r.id}/status`;

                    const select = document.getElementById('statusSelect');
                    if (select.tomselect) {
                        select.tomselect.setValue(r.status);
                    } else {
                        select.value = r.status;
                    }

                    const btnUpdate = document.getElementById('btnUpdateStatus');

                    if (select.tomselect) select.tomselect.enable();
                    select.disabled = false;
                    btnUpdate.disabled = false;
                    btnUpdate.classList.remove('opacity-50', 'cursor-not-allowed');

                    handleStatusChange(r.status);
                }

                loading.classList.add('hidden');
                content.classList.remove('hidden');
                lucide.createIcons();
            })
            .catch(err => {
                alert('Terjadi kesalahan memuat detail: ' + err);
                closeDetailModal();
            });
    }

    function closeDetailModal() {
        document.getElementById('detailModal').classList.add('hidden');
    }

    function openStatusUpdateShortcutModal(id, currentStatus, trackingCode) {
        const modal = document.getElementById('statusShortcutModal');
        const form = document.getElementById('shortcutStatusForm');
        const trackingEl = document.getElementById('shortcutModalTrackingCode');
        const select = document.getElementById('shortcutStatusSelect');
        const notes = document.getElementById('shortcutNotes');

        form.action = `{{ url('/admin/reports') }}/${id}/status`;
        trackingEl.textContent = trackingCode;
        if (select.tomselect) {
            select.tomselect.setValue(currentStatus);
        } else {
            select.value = currentStatus;
        }
        notes.value = '';

        modal.classList.remove('hidden');
        lucide.createIcons();
    }

    function closeStatusUpdateShortcutModal() {
        document.getElementById('statusShortcutModal').classList.add('hidden');
    }

    function handleStatusChange(val) {
        const field = document.getElementById('rejectionNoteField');
        const textarea = document.getElementById('modalNotes');
        if (val === 'rejected') {
            field.classList.remove('hidden');
            textarea.required = true;
        } else {
            field.classList.add('hidden');
            textarea.required = false;
        }
    }
</script>
@endsection
