<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Akun Warga - Waste Management</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (Play CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
                    colors: {
                        border: '#E2E8F0',
                        input: '#E2E8F0',
                        ring: '#15803D',
                        background: '#F8FAFC',
                        foreground: '#0F172A',
                        primary: {
                            DEFAULT: '#15803D',
                            hover: '#166534',
                            foreground: '#FFFFFF',
                        },
                        muted: {
                            DEFAULT: '#F1F5F9',
                            foreground: '#64748B',
                        },
                        destructive: {
                            DEFAULT: '#DC2626',
                            foreground: '#FFFFFF',
                        },
                        card: {
                            DEFAULT: '#FFFFFF',
                            foreground: '#0F172A',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="h-full bg-background font-sans text-foreground antialiased flex items-center justify-center p-4">
    <div class="w-full max-w-md my-8">
        <!-- Brand Header -->
        <div class="flex flex-col items-center text-center mb-6">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white shadow-md border border-border p-1.5 mb-3">
                <img src="{{ asset('images/logo.png') }}" alt="Waste Management Logo" class="h-full w-full object-contain rounded-xl">
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-foreground">Waste Management</h1>
            <p class="text-xs text-muted-foreground mt-1">Pendaftaran Akun Warga Pengelolaan Sampah</p>
        </div>

        <!-- Register Card -->
        <div class="rounded-2xl border border-border bg-card p-6 sm:p-8 shadow-xs">
            <div class="mb-5">
                <h2 class="text-lg font-bold tracking-tight text-foreground">Buat Akun Baru</h2>
                <p class="text-xs text-muted-foreground mt-1">Daftar untuk memantau dan melaporkan pengelolaan sampah.</p>
            </div>

            <!-- Global Error Banner -->
            @if ($errors->any())
                <div class="mb-5 flex items-start gap-3 rounded-xl border border-destructive/30 bg-destructive/10 p-3.5 text-destructive text-xs">
                    <i data-lucide="alert-circle" class="h-4 w-4 shrink-0 mt-0.5"></i>
                    <div>
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Form -->
            <form action="{{ route('admin.register.submit') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Name Field -->
                <div>
                    <label for="name" class="block text-xs font-semibold text-foreground mb-1.5">Nama Lengkap</label>
                    <div class="relative">
                        <i data-lucide="user" class="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground"></i>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                               placeholder="Nama lengkap Anda"
                               class="w-full rounded-lg border border-input bg-card pl-9 pr-3 py-2 text-sm text-foreground shadow-xs placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary @error('name') border-destructive @enderror">
                    </div>
                    @error('name')
                        <p class="mt-1 text-xs text-destructive">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Username Field -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-foreground mb-1.5">Username</label>
                    <div class="relative">
                        <i data-lucide="at-sign" class="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground"></i>
                        <input type="text" name="email" id="email" value="{{ old('email') }}" required
                               placeholder="Pilih username unik Anda"
                               class="w-full rounded-lg border border-input bg-card pl-9 pr-3 py-2 text-sm text-foreground shadow-xs placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary @error('email') border-destructive @enderror">
                    </div>
                    @error('email')
                        <p class="mt-1 text-xs text-destructive">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone Field -->
                <div>
                    <label for="phone_number" class="block text-xs font-semibold text-foreground mb-1.5">Nomor Telepon / WhatsApp (Opsional)</label>
                    <div class="relative">
                        <i data-lucide="phone" class="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground"></i>
                        <input type="tel" name="phone_number" id="phone_number" value="{{ old('phone_number') }}"
                               placeholder="Contoh: 081234567890"
                               class="w-full rounded-lg border border-input bg-card pl-9 pr-3 py-2 text-sm text-foreground shadow-xs placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary @error('phone_number') border-destructive @enderror">
                    </div>
                    @error('phone_number')
                        <p class="mt-1 text-xs text-destructive">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-foreground mb-1.5">Kata Sandi</label>
                    <div class="relative">
                        <i data-lucide="lock" class="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground"></i>
                        <input type="password" name="password" id="password" required minlength="6"
                               placeholder="Minimal 6 karakter"
                               class="w-full rounded-lg border border-input bg-card pl-9 pr-10 py-2 text-sm text-foreground shadow-xs placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary @error('password') border-destructive @enderror">
                        <button type="button" onclick="togglePasswordVisibility('password', 'eyeIconPassword')" class="absolute right-3 top-2.5 text-muted-foreground hover:text-foreground">
                            <i data-lucide="eye" id="eyeIconPassword" class="h-4 w-4"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-xs text-destructive">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Confirmation Field -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-foreground mb-1.5">Konfirmasi Kata Sandi</label>
                    <div class="relative">
                        <i data-lucide="shield-check" class="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground"></i>
                        <input type="password" name="password_confirmation" id="password_confirmation" required minlength="6"
                               placeholder="Ulangi kata sandi"
                               class="w-full rounded-lg border border-input bg-card pl-9 pr-10 py-2 text-sm text-foreground shadow-xs placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                        <button type="button" onclick="togglePasswordVisibility('password_confirmation', 'eyeIconConfirm')" class="absolute right-3 top-2.5 text-muted-foreground hover:text-foreground">
                            <i data-lucide="eye" id="eyeIconConfirm" class="h-4 w-4"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-primary py-2.5 px-4 text-sm font-semibold text-primary-foreground shadow-xs hover:bg-primary-hover focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary transition-colors">
                    <i data-lucide="user-plus" class="h-4 w-4"></i>
                    <span>Daftar Sekarang</span>
                </button>

                <!-- Login Link -->
                <div class="pt-4 text-center text-xs text-muted-foreground border-t border-border">
                    Sudah memiliki akun?
                    <a href="{{ route('admin.login') }}" class="font-semibold text-primary hover:underline">
                        Masuk di sini
                    </a>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <p class="text-center text-xs text-muted-foreground mt-6">
            &copy; {{ date('Y') }} Waste Management. Seluruh hak cipta dilindungi.
        </p>
    </div>

    <script>
        lucide.createIcons();

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
</body>
</html>
