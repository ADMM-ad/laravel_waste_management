<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrator - Smart Waste Management</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (Play CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        border: 'hsl(var(--border))',
                        input: 'hsl(var(--input))',
                        ring: 'hsl(var(--ring))',
                        background: 'hsl(var(--background))',
                        foreground: 'hsl(var(--foreground))',
                        primary: {
                            DEFAULT: 'hsl(var(--primary))',
                            foreground: 'hsl(var(--primary-foreground))',
                        },
                        muted: {
                            DEFAULT: 'hsl(var(--muted))',
                            foreground: 'hsl(var(--muted-foreground))',
                        },
                        destructive: {
                            DEFAULT: 'hsl(var(--destructive))',
                            foreground: 'hsl(var(--destructive-foreground))',
                        },
                        card: {
                            DEFAULT: 'hsl(var(--card))',
                            foreground: 'hsl(var(--card-foreground))',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        :root {
            --background: 0 0% 100%;
            --foreground: 240 10% 3.9%;
            --card: 0 0% 100%;
            --card-foreground: 240 10% 3.9%;
            --primary: 240 5.9% 10%;
            --primary-foreground: 0 0% 98%;
            --muted: 240 4.8% 95.9%;
            --muted-foreground: 240 3.8% 46.1%;
            --destructive: 0 84.2% 60.2%;
            --destructive-foreground: 0 0% 98%;
            --border: 240 5.9% 90%;
            --input: 240 5.9% 90%;
            --ring: 240 5.9% 10%;
            --radius: 0.5rem;
        }
        .dark {
            --background: 240 10% 3.9%;
            --foreground: 0 0% 98%;
            --card: 240 10% 3.9%;
            --card-foreground: 0 0% 98%;
            --primary: 0 0% 98%;
            --primary-foreground: 240 5.9% 10%;
            --muted: 240 3.7% 15.9%;
            --muted-foreground: 240 5% 64.9%;
            --destructive: 0 62.8% 30.6%;
            --destructive-foreground: 0 0% 98%;
            --border: 240 3.7% 15.9%;
            --input: 240 3.7% 15.9%;
            --ring: 240 4.9% 83.9%;
        }
    </style>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="h-full bg-muted/40 font-sans text-foreground antialiased flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Brand Header -->
        <div class="flex flex-col items-center text-center mb-8">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary text-primary-foreground shadow-md mb-3">
                <i data-lucide="trash-2" class="h-6 w-6"></i>
            </div>
            <h1 class="text-2xl font-bold tracking-tight">Smart Waste System</h1>
            <p class="text-sm text-muted-foreground mt-1">Portal Masuk Administrator Pengelolaan Sampah</p>
        </div>

        <!-- Shadcn Card -->
        <div class="rounded-xl border border-border bg-card p-6 sm:p-8 shadow-sm">
            <div class="mb-6">
                <h2 class="text-lg font-semibold tracking-tight">Masuk ke Akun Anda</h2>
                <p class="text-xs text-muted-foreground mt-1">Gunakan akun admin terdaftar untuk mengelola sistem.</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 rounded-lg border border-destructive/30 bg-destructive/10 p-3 text-destructive text-sm flex items-start gap-2.5">
                    <i data-lucide="alert-circle" class="h-4 w-4 mt-0.5 shrink-0"></i>
                    <div>
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-4">
                @csrf
                <div class="space-y-1.5">
                    <label for="email" class="text-xs font-medium text-foreground">Alamat Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', 'admin@banksampah.id') }}" required autofocus
                           class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-xs placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                </div>

                <div class="space-y-1.5">
                    <label for="password" class="text-xs font-medium text-foreground">Kata Sandi</label>
                    <input type="password" id="password" name="password" value="password123" required
                           class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-xs placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-input text-primary focus:ring-ring">
                        <span>Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-md bg-primary py-2.5 px-4 text-sm font-medium text-primary-foreground shadow-xs hover:bg-primary/90 transition-colors">
                    <i data-lucide="log-in" class="h-4 w-4"></i>
                    <span>Masuk ke Dashboard</span>
                </button>
            </form>

            <div class="mt-6 border-t border-border pt-4 text-center">
                <span class="text-[11px] text-muted-foreground">Default Superadmin: <strong>admin@banksampah.id</strong> / <strong>password123</strong></span>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
