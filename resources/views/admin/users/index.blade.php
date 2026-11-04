@extends('layouts.master')

@section('title', 'Manajemen Pengguna')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-foreground">Pengguna Terdaftar</h1>
            <p class="text-sm text-muted-foreground">Kelola akun warga dan hak akses administrator portal.</p>
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="rounded-xl border border-border bg-card p-4 shadow-xs">
        <form method="GET" action="{{ route('admin.users.index') }}" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4 items-center">
            <!-- Search Input -->
            <div class="relative sm:col-span-2">
                <i data-lucide="search" class="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground"></i>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari nama, email, atau no. telepon..."
                       class="w-full rounded-md border border-input bg-background pl-9 pr-3 py-2 text-xs shadow-xs placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-ring">
            </div>

            <!-- Active Status Filter (Searchable TomSelect) -->
            <div class="w-full min-w-0">
                <select name="is_active" class="tom-select rounded-md border border-input bg-background text-xs shadow-xs" placeholder="Cari Status Akun...">
                    <option value="all">Semua Status Akun</option>
                    <option value="true" {{ ($filters['is_active'] ?? '') === 'true' ? 'selected' : '' }}>Aktif</option>
                    <option value="false" {{ ($filters['is_active'] ?? '') === 'false' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <!-- Buttons: Terapkan & Reset -->
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-md bg-primary py-2 px-3 text-xs font-medium text-primary-foreground shadow-xs hover:bg-primary/90 transition-colors">
                    <i data-lucide="filter" class="h-3.5 w-3.5"></i>
                    <span>Terapkan</span>
                </button>
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center gap-1.5 rounded-md border border-input bg-background py-2 px-3 text-xs font-medium text-muted-foreground hover:bg-accent hover:text-foreground transition-colors" title="Reset Filter">
                    <i data-lucide="rotate-ccw" class="h-3.5 w-3.5"></i>
                    <span>Reset</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Users Table (Shadcn Table) -->
    <div class="rounded-xl border border-border bg-card shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-border bg-muted/40 text-xs font-medium text-muted-foreground uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3.5">Nama & Profil</th>
                        <th class="px-5 py-3.5">Kontak</th>
                        <th class="px-5 py-3.5">Peran (Role)</th>
                        <th class="px-5 py-3.5">Status Akun</th>
                        <th class="px-5 py-3.5">Terdaftar</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse ($users as $user)
                        <tr class="hover:bg-muted/30 transition-colors">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-secondary text-secondary-foreground font-semibold text-xs border border-border">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-xs text-foreground">{{ $user->name }}</div>
                                        <div class="text-[11px] text-muted-foreground">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-xs text-muted-foreground">
                                {{ $user->phone_number ?: '-' }}
                            </td>
                            <td class="px-5 py-4">
                                @foreach ($user->roles as $role)
                                    @if ($role->name === 'admin')
                                        <span class="inline-flex items-center rounded-md bg-purple-500/10 border border-purple-500/20 px-2 py-0.5 text-xs font-semibold text-purple-700 dark:text-purple-400">
                                            Admin
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-md bg-secondary border border-border px-2 py-0.5 text-xs font-medium text-secondary-foreground">
                                            Warga
                                        </span>
                                    @endif
                                @endforeach
                            </td>
                            <td class="px-5 py-4">
                                @if ($user->is_active)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-500/10 px-2.5 py-0.5 text-[11px] font-semibold text-emerald-600 dark:text-emerald-400">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-rose-500/10 px-2.5 py-0.5 text-[11px] font-semibold text-rose-600 dark:text-rose-400">
                                        <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span> Dinonaktifkan
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-xs text-muted-foreground">
                                {{ $user->created_at->format('d M Y') }}
                            </td>
                            <td class="px-5 py-4 text-right">
                                @if ($user->hasRole('admin'))
                                    <span class="inline-flex items-center gap-1.5 rounded-md bg-muted px-2.5 py-1 text-xs font-semibold text-muted-foreground border border-border">
                                        <i data-lucide="shield" class="h-3.5 w-3.5 text-primary"></i>
                                        <span>Admin (Dilindungi)</span>
                                    </span>
                                @else
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- Toggle Status Button (Custom Themed Modal) -->
                                        <button type="button"
                                                onclick="openStatusConfirmationModal('{{ route('admin.users.toggle-status', $user->id) }}', '{{ $user->name }}', {{ $user->is_active ? 'true' : 'false' }})"
                                                class="inline-flex items-center gap-1 rounded-md border border-input bg-background px-2.5 py-1.5 text-xs font-medium shadow-xs hover:bg-accent transition-colors"
                                                title="Ubah Status Aktif">
                                            <i data-lucide="{{ $user->is_active ? 'user-x' : 'user-check' }}" class="h-3.5 w-3.5"></i>
                                            <span>{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</span>
                                        </button>

                                        <!-- Delete Button -->
                                        <button type="button"
                                                onclick="openDeleteConfirmationModal('{{ route('admin.users.destroy', $user->id) }}', 'Hapus Pengguna {{ $user->name }}', 'Apakah Anda yakin ingin menghapus data pengguna {{ $user->name }}? Data riwayat laporan yang bersangkutan akan tetap disimpan di sistem.')"
                                                class="inline-flex items-center justify-center rounded-md border border-destructive/30 bg-destructive/10 p-1.5 text-xs text-destructive hover:bg-destructive hover:text-destructive-foreground transition-colors"
                                                title="Hapus Pengguna">
                                            <i data-lucide="trash-2" class="h-3.5 w-3.5"></i>
                                        </button>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-sm text-muted-foreground">
                                <i data-lucide="users" class="h-8 w-8 mx-auto mb-2 opacity-50"></i>
                                Tidak ada data pengguna yang sesuai pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="border-t border-border p-4">
                {{ $users->links() }}
            </div>
        @endif
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

<!-- Custom Status Confirmation Modal (Shadcn Alert Dialog Style) -->
<div id="statusConfirmModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-xs transition-opacity" onclick="closeStatusConfirmationModal()"></div>
    <div class="relative w-full max-w-md rounded-2xl border border-border bg-card p-6 shadow-2xl text-card-foreground">
        <div class="flex items-start gap-4">
            <div id="statusModalIconContainer" class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-amber-500/10 text-amber-600">
                <i data-lucide="user-x" id="statusModalIcon" class="h-6 w-6"></i>
            </div>
            <div class="flex-1 space-y-1">
                <h3 class="text-base font-bold text-foreground tracking-tight" id="statusModalTitle">Ubah Status Akun</h3>
                <p class="text-xs text-muted-foreground leading-relaxed" id="statusModalMessage">Apakah Anda yakin ingin mengubah status pengguna ini?</p>
            </div>
        </div>

        <form id="statusConfirmForm" method="POST" action="" class="mt-6 flex items-center justify-end gap-2.5">
            @csrf
            <button type="button" onclick="closeStatusConfirmationModal()"
                    class="rounded-lg border border-input bg-background px-4 py-2 text-xs font-medium text-foreground shadow-xs hover:bg-accent transition-colors">
                Batal
            </button>
            <button type="submit" id="statusModalSubmitBtn"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-amber-600 px-4 py-2 text-xs font-semibold text-white shadow-xs hover:bg-amber-700 transition-colors">
                <i data-lucide="check" class="h-3.5 w-3.5"></i>
                <span id="statusModalSubmitText">Ya, Ubah Status</span>
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openStatusConfirmationModal(actionUrl, userName, isActive) {
        const modal = document.getElementById('statusConfirmModal');
        const form = document.getElementById('statusConfirmForm');
        const titleEl = document.getElementById('statusModalTitle');
        const msgEl = document.getElementById('statusModalMessage');
        const iconContainer = document.getElementById('statusModalIconContainer');
        const icon = document.getElementById('statusModalIcon');
        const submitBtn = document.getElementById('statusModalSubmitBtn');
        const submitText = document.getElementById('statusModalSubmitText');

        form.action = actionUrl;

        if (isActive) {
            titleEl.innerText = 'Nonaktifkan Akun ' + userName;
            msgEl.innerText = 'Akun pengguna ini akan dinonaktifkan sementara dan tidak dapat masuk ke sistem.';
            iconContainer.className = 'flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-amber-500/10 text-amber-600';
            icon.setAttribute('data-lucide', 'user-x');
            submitBtn.className = 'inline-flex items-center gap-1.5 rounded-lg bg-amber-600 px-4 py-2 text-xs font-semibold text-white shadow-xs hover:bg-amber-700 transition-colors';
            submitText.innerText = 'Ya, Nonaktifkan Akun';
        } else {
            titleEl.innerText = 'Aktifkan Akun ' + userName;
            msgEl.innerText = 'Akun pengguna ini akan diaktifkan kembali sehingga dapat masuk dan menggunakan sistem.';
            iconContainer.className = 'flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-emerald-500/10 text-emerald-600';
            icon.setAttribute('data-lucide', 'user-check');
            submitBtn.className = 'inline-flex items-center gap-1.5 rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-white shadow-xs hover:bg-primary/90 transition-colors';
            submitText.innerText = 'Ya, Aktifkan Akun';
        }

        modal.classList.remove('hidden');
        lucide.createIcons();
    }

    function closeStatusConfirmationModal() {
        document.getElementById('statusConfirmModal').classList.add('hidden');
    }

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
</script>
@endpush
@endsection
