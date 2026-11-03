@extends('layouts.master')

@section('title', 'Profil Administrator')

@section('content')
<div class="space-y-6 max-w-5xl">
    <!-- Header Banner -->
    <div class="rounded-xl border border-border bg-card p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="h-16 w-16 rounded-full bg-primary/10 text-primary border-2 border-primary/20 flex items-center justify-center font-bold text-2xl">
                {{ strtoupper(substr($user->name ?? 'AD', 0, 2)) }}
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl font-bold tracking-tight text-foreground">{{ $user->name }}</h1>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-primary/10 text-primary">
                        Administrator
                    </span>
                </div>
                <p class="text-sm text-muted-foreground">{{ $user->email }} • Terdaftar sejak {{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2 text-xs text-muted-foreground bg-background px-3 py-2 rounded-lg border border-border">
            <i data-lucide="shield-check" class="h-4 w-4 text-primary"></i>
            <span>Hak Akses: Penuh (Sistem Manajemen Sampah)</span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Card 1: Informasi Profil -->
        <div class="rounded-xl border border-border bg-card shadow-xs overflow-hidden">
            <div class="border-b border-border px-6 py-4 bg-muted/20">
                <h2 class="text-base font-semibold text-foreground flex items-center gap-2">
                    <i data-lucide="user" class="h-4 w-4 text-primary"></i>
                    Informasi Akun
                </h2>
                <p class="text-xs text-muted-foreground">Perbarui nama lengkap, email resmi, dan nomor kontak aktif.</p>
            </div>
            <form action="{{ route('admin.profile.update') }}" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-xs font-semibold text-foreground mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                           class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm text-foreground shadow-xs focus:outline-none focus:ring-1 focus:ring-primary @error('name') border-destructive @enderror">
                    @error('name')
                        <p class="mt-1 text-xs text-destructive">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold text-foreground mb-1.5">Alamat Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                           class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm text-foreground shadow-xs focus:outline-none focus:ring-1 focus:ring-primary @error('email') border-destructive @enderror">
                    @error('email')
                        <p class="mt-1 text-xs text-destructive">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone_number" class="block text-xs font-semibold text-foreground mb-1.5">Nomor Telepon / WhatsApp</label>
                    <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $user->phone_number) }}"
                           placeholder="Contoh: 081234567890"
                           class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm text-foreground shadow-xs focus:outline-none focus:ring-1 focus:ring-primary @error('phone_number') border-destructive @enderror">
                    @error('phone_number')
                        <p class="mt-1 text-xs text-destructive">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-semibold text-primary-foreground shadow-xs hover:bg-primary/90 transition-colors">
                        <i data-lucide="save" class="h-4 w-4"></i>
                        Simpan Profil
                    </button>
                </div>
            </form>
        </div>

        <!-- Card 2: Keamanan & Kata Sandi -->
        <div class="rounded-xl border border-border bg-card shadow-xs overflow-hidden">
            <div class="border-b border-border px-6 py-4 bg-muted/20">
                <h2 class="text-base font-semibold text-foreground flex items-center gap-2">
                    <i data-lucide="key" class="h-4 w-4 text-primary"></i>
                    Ganti Kata Sandi
                </h2>
                <p class="text-xs text-muted-foreground">Pastikan kata sandi baru menggunakan minimal 8 karakter yang aman.</p>
            </div>
            <form action="{{ route('admin.profile.password') }}" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" class="block text-xs font-semibold text-foreground mb-1.5">Kata Sandi Saat Ini</label>
                    <div class="relative">
                        <input type="password" name="current_password" id="current_password" required
                               class="w-full rounded-md border border-input bg-background pl-3 pr-10 py-2 text-sm text-foreground shadow-xs focus:outline-none focus:ring-1 focus:ring-primary @error('current_password') border-destructive @enderror">
                        <button type="button" onclick="togglePasswordVisibility('current_password', 'eyeCurrentPassword')" class="absolute right-3 top-2.5 text-muted-foreground hover:text-foreground">
                            <i data-lucide="eye" id="eyeCurrentPassword" class="h-4 w-4"></i>
                        </button>
                    </div>
                    @error('current_password')
                        <p class="mt-1 text-xs text-destructive">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-foreground mb-1.5">Kata Sandi Baru</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" required minlength="8"
                               class="w-full rounded-md border border-input bg-background pl-3 pr-10 py-2 text-sm text-foreground shadow-xs focus:outline-none focus:ring-1 focus:ring-primary @error('password') border-destructive @enderror">
                        <button type="button" onclick="togglePasswordVisibility('password', 'eyeNewPassword')" class="absolute right-3 top-2.5 text-muted-foreground hover:text-foreground">
                            <i data-lucide="eye" id="eyeNewPassword" class="h-4 w-4"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-xs text-destructive">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-foreground mb-1.5">Konfirmasi Kata Sandi Baru</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="password_confirmation" required minlength="8"
                               class="w-full rounded-md border border-input bg-background pl-3 pr-10 py-2 text-sm text-foreground shadow-xs focus:outline-none focus:ring-1 focus:ring-primary">
                        <button type="button" onclick="togglePasswordVisibility('password_confirmation', 'eyeConfirmPassword')" class="absolute right-3 top-2.5 text-muted-foreground hover:text-foreground">
                            <i data-lucide="eye" id="eyeConfirmPassword" class="h-4 w-4"></i>
                        </button>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-md bg-slate-900 text-white px-4 py-2 text-sm font-semibold shadow-xs hover:bg-slate-800 transition-colors">
                        <i data-lucide="lock" class="h-4 w-4"></i>
                        Perbarui Kata Sandi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function togglePasswordVisibility(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input && icon) {
            if (input.type === 'password') {
                input.type = 'text';
                icon.setAttribute('data-lucide', 'eye-off');
            } else {
                input.type = 'password';
                icon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        }
    }
</script>
@endpush
@endsection
