<!DOCTYPE html>
<html lang="id" class="min-h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Waste Management Portal</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

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

    <style>
        .login-bg-pattern {
            background-color: #F8FAFC;
            background-image: radial-gradient(#CBD5E1 1.2px, transparent 1.2px);
            background-size: 24px 24px;
        }
    </style>
</head>
<body class="min-h-screen lg:h-screen lg:overflow-hidden login-bg-pattern font-sans text-foreground antialiased flex flex-col justify-center items-center py-6 sm:py-8 lg:py-0 px-4 overflow-y-auto lg:overflow-hidden relative selection:bg-primary selection:text-white">
    <!-- Ambient Themed Decorative Elements -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        <div class="absolute -top-32 -right-32 w-96 h-96 rounded-full bg-emerald-500/10 blur-3xl"></div>
        <div class="absolute -bottom-32 -left-32 w-96 h-96 rounded-full bg-primary/10 blur-3xl"></div>
    </div>

    <!-- Main Container: Split Card (2 Columns on Laptop/Desktop) -->
    <div class="w-full max-w-md lg:max-w-4xl xl:max-w-5xl rounded-2xl lg:rounded-3xl border border-border bg-card shadow-2xl overflow-hidden my-auto relative z-10 grid grid-cols-1 lg:grid-cols-12 transition-all">
        
        <!-- LEFT COLUMN: Brand Header & Login Form (6 cols on lg:) -->
        <div class="lg:col-span-6 p-6 sm:p-7 lg:p-7 xl:p-8 flex flex-col justify-between">
            <div>
                <!-- Brand Header -->
                <div class="flex items-center gap-3 mb-4 lg:mb-3.5">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white shadow-xs border border-border p-1 shrink-0">
                        <img src="{{ asset('images/logo.png') }}" alt="Waste Management Logo" class="h-full w-full object-contain rounded-lg">
                    </div>
                    <div>
                        <h1 class="text-lg font-bold tracking-tight text-foreground leading-tight">Waste Management</h1>
                        <p class="text-[11px] text-muted-foreground font-medium">Sistem Pengelolaan Sampah & Klasifikasi Cerdas</p>
                    </div>
                </div>

                <!-- Form Title -->
                <div class="mb-3.5">
                    <h2 class="text-base font-bold tracking-tight text-foreground">Masuk ke Portal</h2>
                    <p class="text-xs text-muted-foreground mt-0.5">Silakan masukkan kredensial akun Anda untuk melanjutkan.</p>
                </div>

                <!-- Global Error Banner -->
                @if ($errors->any())
                    <div class="mb-3.5 flex items-start gap-2.5 rounded-xl border border-destructive/30 bg-destructive/10 p-2.5 text-destructive text-xs">
                        <i data-lucide="alert-circle" class="h-4 w-4 shrink-0 mt-0.5"></i>
                        <div>
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Login Form -->
                <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-3">
                    @csrf

                    <!-- Username / Email Field -->
                    <div>
                        <label for="email" class="block text-xs font-semibold text-foreground mb-1">Username / Email</label>
                        <div class="relative">
                            <i data-lucide="user" class="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground"></i>
                            <input type="text" name="email" id="email" value="{{ old('email') }}" required autofocus
                                   placeholder="Contoh: admin atau user"
                                   class="w-full rounded-lg border border-input bg-card pl-9 pr-3 py-2 text-xs sm:text-sm text-foreground shadow-xs placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary @error('email') border-destructive @enderror">
                        </div>
                        @error('email')
                            <p class="mt-1 text-xs text-destructive">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="password" class="block text-xs font-semibold text-foreground">Kata Sandi</label>
                        </div>
                        <div class="relative">
                            <i data-lucide="lock" class="absolute left-3 top-2.5 h-4 w-4 text-muted-foreground"></i>
                            <input type="password" name="password" id="password" required
                                   placeholder="••••••••"
                                   class="w-full rounded-lg border border-input bg-card pl-9 pr-10 py-2 text-xs sm:text-sm text-foreground shadow-xs placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary @error('password') border-destructive @enderror">
                            <button type="button" onclick="togglePasswordVisibility()" class="absolute right-3 top-2.5 text-muted-foreground hover:text-foreground focus:outline-none">
                                <i data-lucide="eye" id="eyeIcon" class="h-4 w-4"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1 text-xs text-destructive">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center gap-2 pt-0.5">
                        <input type="checkbox" name="remember" id="remember"
                               class="h-3.5 w-3.5 rounded border-input text-primary focus:ring-primary cursor-pointer">
                        <label for="remember" class="text-xs text-muted-foreground select-none cursor-pointer">
                            Ingat saya di perangkat ini
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-primary py-2 px-4 text-xs sm:text-sm font-semibold text-primary-foreground shadow-xs hover:bg-primary-hover focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary transition-colors cursor-pointer mt-1">
                        <i data-lucide="log-in" class="h-4 w-4"></i>
                        <span>Masuk ke Sistem</span>
                    </button>

                    <!-- Register Link -->
                    <div class="pt-2.5 text-center text-xs text-muted-foreground border-t border-border mt-2.5">
                        Belum memiliki akun?
                        <a href="{{ route('admin.register') }}" class="font-semibold text-primary hover:underline">
                            Daftar akun warga di sini
                        </a>
                    </div>
                </form>
            </div>

            <!-- Footer Small -->
            <p class="text-[11px] text-muted-foreground text-center lg:text-left pt-3 mt-3">
                &copy; {{ date('Y') }} Waste Management. All rights reserved.
            </p>
        </div>

        <!-- RIGHT COLUMN: Themed Vector Illustration & Features (6 cols on lg:) -->
        <div class="hidden lg:flex lg:col-span-6 bg-gradient-to-br from-emerald-50/90 via-slate-50 to-emerald-100/40 border-l border-border p-6 lg:p-7 xl:p-8 flex-col justify-between relative overflow-hidden">
            <!-- Background Decorative circles -->
            <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-emerald-500/10 blur-2xl pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-64 h-64 rounded-full bg-primary/10 blur-2xl pointer-events-none"></div>

            <!-- Top Tagline & Badge -->
            <div class="relative z-10">
                <div class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/10 px-2.5 py-0.5 text-xs font-semibold text-emerald-800 border border-emerald-500/20 shadow-xs mb-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                    Smart Eco Platform
                </div>
                <h3 class="text-lg font-bold tracking-tight text-foreground leading-snug">
                    Pengelolaan Sampah Terpadu & Berkelanjutan
                </h3>
                <p class="text-xs text-muted-foreground mt-1 leading-relaxed">
                    Mewujudkan lingkungan bersih dan bebas timbunan sampah melalui pelaporan warga cepat dan verifikasi cerdas berbasis AI.
                </p>
            </div>

            <!-- Centered Vector Illustration (WebP) -->
            <div class="relative z-10 my-2.5 flex items-center justify-center">
                <div class="rounded-xl border border-emerald-500/20 bg-white/80 p-2.5 shadow-sm backdrop-blur-xs transition-transform hover:scale-[1.01] duration-300">
                    <img src="{{ asset('images/login-illustration.webp') }}" 
                         alt="Ilustrasi Pengelolaan Sampah"
                         class="w-full h-auto max-h-44 xl:max-h-52 object-contain rounded-lg">
                </div>
            </div>

            <!-- Bottom Feature Points -->
            <div class="relative z-10 grid grid-cols-2 gap-2.5 pt-3 border-t border-emerald-900/10">
                <div class="flex items-center gap-2">
                    <div class="flex h-6 w-6 items-center justify-center rounded-md bg-emerald-600/10 text-emerald-700 shrink-0">
                        <i data-lucide="cpu" class="h-3 w-3"></i>
                    </div>
                    <span class="text-[11px] font-semibold text-foreground">Deteksi AI Otomatis</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="flex h-6 w-6 items-center justify-center rounded-md bg-emerald-600/10 text-emerald-700 shrink-0">
                        <i data-lucide="map-pin" class="h-3 w-3"></i>
                    </div>
                    <span class="text-[11px] font-semibold text-foreground">Pemetaan GPS Akurat</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Password Visibility Script -->
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
