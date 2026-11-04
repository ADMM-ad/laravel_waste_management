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
        <div>
            <button type="button" onclick="openCreateUserModal()"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-primary-foreground shadow-xs hover:bg-primary/90 transition-colors">
                <i data-lucide="user-plus" class="h-4 w-4"></i>
                <span>Tambah Pengguna Baru</span>
            </button>
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
                                                class="inline-flex items-center gap-1 rounded-md border border-destructive/30 bg-destructive/10 px-2.5 py-1.5 text-xs font-medium text-destructive hover:bg-destructive hover:text-destructive-foreground transition-colors"
                                                title="Hapus Pengguna">
                                            <i data-lucide="trash-2" class="h-3.5 w-3.5"></i>
                                            <span>Hapus</span>
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

<!-- Create User Modal (Shadcn Dialog Style) -->
<div id="createUserModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
    <div class="w-full max-w-lg rounded-2xl border border-border bg-card p-5 sm:p-6 text-card-foreground shadow-2xl my-6">
        <div class="flex items-center justify-between border-b border-border pb-3 mb-4">
            <div class="flex items-center gap-2.5">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-500/10 text-emerald-600">
                    <i data-lucide="user-plus" class="h-5 w-5"></i>
                </div>
                <div>
                    <h3 class="font-bold text-base text-foreground">Tambah Pengguna Baru</h3>
                    <p class="text-xs text-muted-foreground">Daftarkan akun administrator atau warga baru.</p>
                </div>
            </div>
            <button type="button" onclick="closeCreateUserModal()" class="inline-flex h-8 w-8 items-center justify-center rounded-md text-muted-foreground hover:bg-accent transition-colors">
                <i data-lucide="x" class="h-4 w-4"></i>
            </button>
        </div>

        <!-- Alert Ringkasan Validasi Server Error -->
        @if ($errors->any())
            <div class="mb-4 rounded-xl border border-destructive/30 bg-destructive/10 p-3.5 text-destructive text-xs space-y-1.5 animate-in fade-in duration-200">
                <div class="flex items-center gap-2 font-semibold">
                    <i data-lucide="alert-circle" class="h-4 w-4 shrink-0"></i>
                    <span>Pendaftaran pengguna gagal! Mohon periksa kesalahan berikut:</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-[11px] text-destructive/90 pl-1 font-medium">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="createUserForm" action="{{ route('admin.users.store') }}" method="POST" novalidate class="space-y-4">
            @csrf

            <!-- Nama Lengkap -->
            <div class="space-y-1">
                <label for="createUserName" class="text-xs font-semibold text-foreground">Nama Lengkap <span class="text-destructive">*</span></label>
                <input type="text" name="name" id="createUserName" value="{{ old('name') }}"
                       placeholder="Contoh: Budi Santoso"
                       class="w-full rounded-md border @error('name') border-destructive focus:ring-destructive @else border-input focus:ring-ring @enderror bg-background p-2.5 text-xs shadow-xs placeholder:text-muted-foreground focus:outline-none focus:ring-1">
                @error('name')
                    <p class="text-[11px] text-destructive font-medium flex items-center gap-1 mt-1">
                        <i data-lucide="alert-circle" class="h-3.5 w-3.5 shrink-0"></i>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
                <p id="clientErrorName" class="hidden text-[11px] text-destructive font-medium flex items-center gap-1 mt-1">
                    <i data-lucide="alert-circle" class="h-3.5 w-3.5 shrink-0"></i>
                    <span>Nama lengkap wajib diisi (minimal 2 karakter).</span>
                </p>
            </div>

            <!-- Username / Email -->
            <div class="space-y-1">
                <label for="createUserEmail" class="text-xs font-semibold text-foreground">Username / Email <span class="text-destructive">*</span></label>
                <input type="text" name="email" id="createUserEmail" value="{{ old('email') }}"
                       placeholder="Contoh: budi@example.com atau budis"
                       class="w-full rounded-md border @error('email') border-destructive focus:ring-destructive @else border-input focus:ring-ring @enderror bg-background p-2.5 text-xs shadow-xs placeholder:text-muted-foreground focus:outline-none focus:ring-1">
                @error('email')
                    <p class="text-[11px] text-destructive font-medium flex items-center gap-1 mt-1">
                        <i data-lucide="alert-circle" class="h-3.5 w-3.5 shrink-0"></i>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
                <p id="clientErrorEmail" class="hidden text-[11px] text-destructive font-medium flex items-center gap-1 mt-1">
                    <i data-lucide="alert-circle" class="h-3.5 w-3.5 shrink-0"></i>
                    <span id="clientErrorEmailText">Username atau email wajib diisi.</span>
                </p>
                <p class="text-[10px] text-muted-foreground">Digunakan untuk login di web maupun aplikasi mobile.</p>
            </div>

            <!-- Nomor Telepon -->
            <div class="space-y-1">
                <label for="createUserPhone" class="text-xs font-semibold text-foreground">Nomor Telepon / WhatsApp <span class="text-muted-foreground text-[10px]">(Opsional)</span></label>
                <input type="text" name="phone_number" id="createUserPhone" value="{{ old('phone_number') }}"
                       placeholder="Contoh: 081234567890"
                       class="w-full rounded-md border @error('phone_number') border-destructive focus:ring-destructive @else border-input focus:ring-ring @enderror bg-background p-2.5 text-xs shadow-xs placeholder:text-muted-foreground focus:outline-none focus:ring-1">
                @error('phone_number')
                    <p class="text-[11px] text-destructive font-medium flex items-center gap-1 mt-1">
                        <i data-lucide="alert-circle" class="h-3.5 w-3.5 shrink-0"></i>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>

            <!-- Password -->
            <div class="space-y-1">
                <label for="createUserPassword" class="text-xs font-semibold text-foreground">Kata Sandi <span class="text-destructive">*</span></label>
                <input type="password" name="password" id="createUserPassword" minlength="6"
                       placeholder="Minimal 6 karakter..."
                       class="w-full rounded-md border @error('password') border-destructive focus:ring-destructive @else border-input focus:ring-ring @enderror bg-background p-2.5 text-xs shadow-xs placeholder:text-muted-foreground focus:outline-none focus:ring-1">
                @error('password')
                    <p class="text-[11px] text-destructive font-medium flex items-center gap-1 mt-1">
                        <i data-lucide="alert-circle" class="h-3.5 w-3.5 shrink-0"></i>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
                <p id="clientErrorPassword" class="hidden text-[11px] text-destructive font-medium flex items-center gap-1 mt-1">
                    <i data-lucide="alert-circle" class="h-3.5 w-3.5 shrink-0"></i>
                    <span>Kata sandi minimal 6 karakter.</span>
                </p>
            </div>

            <!-- Role Selection (Dropdown TomSelect) -->
            <div class="space-y-1">
                <label for="createUserRole" class="text-xs font-semibold text-foreground">Pilih Peran (Role) Pengguna <span class="text-destructive">*</span></label>
                <select name="role" id="createUserRole" class="tom-select w-full" placeholder="Pilih peran pengguna...">
                    <option value="user" {{ old('role', 'user') == 'user' ? 'selected' : '' }}>Warga (User) - Akses Pelaporan Mobile & Web</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrator (Admin) - Akses Penuh Sistem & Kelola User</option>
                </select>
                @error('role')
                    <p class="text-[11px] text-destructive font-medium flex items-center gap-1 mt-1">
                        <i data-lucide="alert-circle" class="h-3.5 w-3.5 shrink-0"></i>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
                <p id="clientErrorRole" class="hidden text-[11px] text-destructive font-medium flex items-center gap-1 mt-1">
                    <i data-lucide="alert-circle" class="h-3.5 w-3.5 shrink-0"></i>
                    <span>Silakan pilih peran pengguna (Warga atau Admin).</span>
                </p>
            </div>

            <!-- Status Aktif Checkbox -->
            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" name="is_active" id="createUserIsActive" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} class="h-4 w-4 rounded border-input text-emerald-600 focus:ring-emerald-500">
                <label for="createUserIsActive" class="text-xs text-foreground font-medium">Aktifkan akun pengguna langsung setelah dibuat</label>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-border">
                <button type="button" onclick="closeCreateUserModal()"
                        class="rounded-lg border border-input bg-background px-4 py-2 text-xs font-medium text-foreground shadow-xs hover:bg-accent transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-primary-foreground shadow-xs hover:bg-primary/90 transition-colors">
                    <i data-lucide="save" class="h-3.5 w-3.5"></i>
                    <span>Simpan Pengguna</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    window.openCreateUserModal = function() {
        const modal = document.getElementById('createUserModal');
        if (!modal) return;
        modal.classList.remove('hidden');
        const roleSelect = document.getElementById('createUserRole');
        if (roleSelect && !roleSelect.tomselect && typeof TomSelect !== 'undefined') {
            new TomSelect(roleSelect, {
                create: false,
                maxItems: 1,
                placeholder: 'Pilih peran pengguna...',
                allowEmptyOption: false,
                closeAfterSelect: true,
            });
        }
        if (window.lucide) {
            lucide.createIcons();
        }
    };

    window.closeCreateUserModal = function() {
        const modal = document.getElementById('createUserModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    };

    // Client-side Interactive Form Validation
    document.addEventListener('DOMContentLoaded', function () {
        @if ($errors->any())
            // Auto open modal on server validation error
            window.openCreateUserModal();
        @endif

        const createUserForm = document.getElementById('createUserForm');
        if (createUserForm) {
            createUserForm.addEventListener('submit', function (e) {
                let isValid = true;
                
                const nameInput = document.getElementById('createUserName');
                const emailInput = document.getElementById('createUserEmail');
                const passwordInput = document.getElementById('createUserPassword');
                const roleSelect = document.getElementById('createUserRole');
                
                const clientErrorName = document.getElementById('clientErrorName');
                const clientErrorEmail = document.getElementById('clientErrorEmail');
                const clientErrorPassword = document.getElementById('clientErrorPassword');
                const clientErrorRole = document.getElementById('clientErrorRole');
                
                function clearFieldError(input, errorEl) {
                    if (input) {
                        input.classList.remove('border-destructive', 'focus:ring-destructive');
                        input.classList.add('border-input', 'focus:ring-ring');
                    }
                    if (errorEl) errorEl.classList.add('hidden');
                }
                
                function showFieldError(input, errorEl) {
                    if (input) {
                        input.classList.add('border-destructive', 'focus:ring-destructive');
                        input.classList.remove('border-input', 'focus:ring-ring');
                    }
                    if (errorEl) errorEl.classList.remove('hidden');
                    isValid = false;
                }

                clearFieldError(nameInput, clientErrorName);
                clearFieldError(emailInput, clientErrorEmail);
                clearFieldError(passwordInput, clientErrorPassword);
                clearFieldError(null, clientErrorRole);

                // Name validation
                if (!nameInput.value.trim() || nameInput.value.trim().length < 2) {
                    showFieldError(nameInput, clientErrorName);
                }

                // Email/username validation
                if (!emailInput.value.trim()) {
                    document.getElementById('clientErrorEmailText').innerText = 'Username atau email wajib diisi.';
                    showFieldError(emailInput, clientErrorEmail);
                } else if (emailInput.value.trim().length < 3) {
                    document.getElementById('clientErrorEmailText').innerText = 'Username atau email minimal 3 karakter.';
                    showFieldError(emailInput, clientErrorEmail);
                }

                // Password validation
                if (!passwordInput.value || passwordInput.value.length < 6) {
                    showFieldError(passwordInput, clientErrorPassword);
                }

                // Role validation
                if (!roleSelect.value || (roleSelect.value !== 'admin' && roleSelect.value !== 'user')) {
                    showFieldError(null, clientErrorRole);
                }

                if (!isValid) {
                    e.preventDefault();
                    if (window.lucide) lucide.createIcons();
                    if (!nameInput.value.trim() || nameInput.value.trim().length < 2) {
                        nameInput.focus();
                    } else if (!emailInput.value.trim() || emailInput.value.trim().length < 3) {
                        emailInput.focus();
                    } else if (!passwordInput.value || passwordInput.value.length < 6) {
                        passwordInput.focus();
                    }
                }
            });

            // Live clear error on typing
            ['createUserName', 'createUserEmail', 'createUserPassword'].forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.addEventListener('input', function() {
                        this.classList.remove('border-destructive', 'focus:ring-destructive');
                        this.classList.add('border-input', 'focus:ring-ring');
                        const errMap = {
                            'createUserName': 'clientErrorName',
                            'createUserEmail': 'clientErrorEmail',
                            'createUserPassword': 'clientErrorPassword'
                        };
                        const errEl = document.getElementById(errMap[id]);
                        if (errEl) errEl.classList.add('hidden');
                    });
                }
            });
        }
    });

    window.openStatusConfirmationModal = function(actionUrl, userName, isActive) {
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
        if (window.lucide) lucide.createIcons();
    };

    window.closeStatusConfirmationModal = function() {
        document.getElementById('statusConfirmModal').classList.add('hidden');
    };

    window.openDeleteConfirmationModal = function(actionUrl, title, message) {
        const modal = document.getElementById('deleteConfirmModal');
        const form = document.getElementById('deleteConfirmForm');
        const titleEl = document.getElementById('deleteModalTitle');
        const msgEl = document.getElementById('deleteModalMessage');

        form.action = actionUrl;
        titleEl.innerText = title;
        msgEl.innerText = message;

        modal.classList.remove('hidden');
        if (window.lucide) lucide.createIcons();
    };

    window.closeDeleteConfirmationModal = function() {
        document.getElementById('deleteConfirmModal').classList.add('hidden');
    };
</script>
@endpush
@endsection
