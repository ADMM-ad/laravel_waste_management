<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Waste Management Portal</title>

    <!-- Google Fonts: Plus Jakarta Sans (DESIGN.md Official) -->
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
    <div class="w-full max-w-md">
        <!-- Brand Header -->
        <div class="flex flex-col items-center text-center mb-8">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white shadow-md border border-border p-1.5 mb-3">
                <img src="{{ asset('images/logo.png') }}" alt="Waste Management Logo" class="h-full w-full object-contain rounded-xl">
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-foreground">Waste Management</h1>
            <p class="text-xs text-muted-foreground mt-1">Sistem Informasi Pengelolaan Sampah & Klasifikasi Cerdas</p>
        </div>

        <!-- Login Card -->
        <div class="rounded-2xl border border-border bg-card p-6 sm:p-8 shadow-xs">
            <div class="mb-6">
                <h2 class="text-lg font-bold tracking-tight text-foreground">Masuk ke Portal</h2>
                <p class="text-xs text-muted-foreground mt-1">Gunakan username dan kata sandi Anda untuk melanjutkan.</p>
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
            <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Username Field -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-foreground mb-1.5">Username</label>
                    <div class="relative">
                        <i data-lucide="user" class="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground"></i>
                        <input type="text" name="email" id="email" value="{{ old('email') }}" required autofocus
                               placeholder="Contoh: admin atau user"
                               class="w-full rounded-lg border border-input bg-card pl-9 pr-3 py-2 text-sm text-foreground shadow-xs placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary @error('email') border-destructive @enderror">
                    </div>
                    @error('email')
                        <p class="mt-1 text-xs text-destructive">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-semibold text-foreground">Kata Sandi</label>
                    </div>
                    <div class="relative">
                        <i data-lucide="lock" class="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground"></i>
                        <input type="password" name="password" id="password" required
                               placeholder="••••••••"
                               class="w-full rounded-lg border border-input bg-card pl-9 pr-10 py-2 text-sm text-foreground shadow-xs placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary @error('password') border-destructive @enderror">
                        <button type="button" onclick="togglePasswordVisibility()" class="absolute right-3 top-2.5 text-muted-foreground hover:text-foreground">
                            <i data-lucide="eye" id="eyeIcon" class="h-4 w-4"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-xs text-destructive">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" name="remember" id="remember"
                           class="h-4 w-4 rounded border-input text-primary focus:ring-primary">
                    <label for="remember" class="text-xs text-muted-foreground select-none cursor-pointer">
                        Ingat saya di perangkat ini
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-primary py-2.5 px-4 text-sm font-semibold text-primary-foreground shadow-xs hover:bg-primary-hover focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary transition-colors">
                    <i data-lucide="log-in" class="h-4 w-4"></i>
                    <span>Masuk ke Sistem</span>
                </button>

                <!-- Register Link -->
                <div class="pt-4 text-center text-xs text-muted-foreground border-t border-border">
                    Belum memiliki akun?
                    <a href="{{ route('admin.register') }}" class="font-semibold text-primary hover:underline">
                        Daftar akun warga di sini
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

        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.setAttribute('data-lucide', 'eye-off');
            } else {
                passwordInput.type = 'password';
                eyeIcon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        }
    </script>
</body>
</html>
