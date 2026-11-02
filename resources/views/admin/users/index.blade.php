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

            <!-- Active Status Filter -->
            <select name="is_active" class="rounded-md border border-input bg-background px-3 py-2 text-xs shadow-xs focus:outline-none">
                <option value="all">Semua Status Akun</option>
                <option value="true" {{ ($filters['is_active'] ?? '') === 'true' ? 'selected' : '' }}>Aktif</option>
                <option value="false" {{ ($filters['is_active'] ?? '') === 'false' ? 'selected' : '' }}>Nonaktif</option>
            </select>

            <!-- Buttons -->
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-md bg-primary py-2 px-3 text-xs font-medium text-primary-foreground shadow-xs hover:bg-primary/90 transition-colors">
                    <i data-lucide="filter" class="h-3.5 w-3.5"></i>
                    <span>Terapkan</span>
                </button>
                @if (!empty($filters['search']) || (isset($filters['is_active']) && $filters['is_active'] !== 'all'))
                    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center rounded-md border border-input bg-background p-2 text-muted-foreground hover:bg-accent transition-colors" title="Reset Filter">
                        <i data-lucide="rotate-ccw" class="h-3.5 w-3.5"></i>
                    </a>
                @endif
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
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Toggle Status Button -->
                                    <form action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                                onclick="return confirm('Apakah Anda yakin ingin mengubah status aktifitas akun {{ $user->name }}?')"
                                                class="inline-flex items-center gap-1 rounded-md border border-input bg-background px-2.5 py-1.5 text-xs font-medium shadow-xs hover:bg-accent transition-colors"
                                                title="Ubah Status Aktif">
                                            <i data-lucide="{{ $user->is_active ? 'user-x' : 'user-check' }}" class="h-3.5 w-3.5"></i>
                                            <span>{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</span>
                                        </button>
                                    </form>

                                    <!-- Delete Button (Only for other accounts) -->
                                    @if ($user->id !== Auth::id())
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    onclick="return confirm('Hapus data pengguna {{ $user->name }}? Data laporan yang bersangkutan akan tetap disimpan.')"
                                                    class="inline-flex items-center justify-center rounded-md border border-destructive/30 bg-destructive/10 p-1.5 text-xs text-destructive hover:bg-destructive hover:text-destructive-foreground transition-colors"
                                                    title="Hapus Pengguna">
                                                <i data-lucide="trash-2" class="h-3.5 w-3.5"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
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
@endsection
