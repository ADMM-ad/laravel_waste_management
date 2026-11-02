<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Informasi Manajemen Sampah - Shadcn UI">
    <title>@yield('title', 'Manajemen Sampah')</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (Play CDN for standalone instant render) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
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
                        secondary: {
                            DEFAULT: 'hsl(var(--secondary))',
                            foreground: 'hsl(var(--secondary-foreground))',
                        },
                        destructive: {
                            DEFAULT: 'hsl(var(--destructive))',
                            foreground: 'hsl(var(--destructive-foreground))',
                        },
                        muted: {
                            DEFAULT: 'hsl(var(--muted))',
                            foreground: 'hsl(var(--muted-foreground))',
                        },
                        accent: {
                            DEFAULT: 'hsl(var(--accent))',
                            foreground: 'hsl(var(--accent-foreground))',
                        },
                        popover: {
                            DEFAULT: 'hsl(var(--popover))',
                            foreground: 'hsl(var(--popover-foreground))',
                        },
                        card: {
                            DEFAULT: 'hsl(var(--card))',
                            foreground: 'hsl(var(--card-foreground))',
                        },
                        sidebar: {
                            DEFAULT: 'hsl(var(--sidebar-background))',
                            foreground: 'hsl(var(--sidebar-foreground))',
                            primary: 'hsl(var(--sidebar-primary))',
                            'primary-foreground': 'hsl(var(--sidebar-primary-foreground))',
                            accent: 'hsl(var(--sidebar-accent))',
                            'accent-foreground': 'hsl(var(--sidebar-accent-foreground))',
                            border: 'hsl(var(--sidebar-border))',
                        }
                    },
                    borderRadius: {
                        lg: 'var(--radius)',
                        md: 'calc(var(--radius) - 2px)',
                        sm: 'calc(var(--radius) - 4px)',
                    }
                }
            }
        }
    </script>

    <!-- Shadcn Design Tokens -->
    <style>
        :root {
            --background: 0 0% 100%;
            --foreground: 240 10% 3.9%;
            --card: 0 0% 100%;
            --card-foreground: 240 10% 3.9%;
            --popover: 0 0% 100%;
            --popover-foreground: 240 10% 3.9%;
            --primary: 152 76% 36%; /* Emerald 600 */
            --primary-foreground: 355 100% 97%;
            --secondary: 240 4.8% 95.9%;
            --secondary-foreground: 240 5.9% 10%;
            --muted: 240 4.8% 95.9%;
            --muted-foreground: 240 3.8% 46.1%;
            --accent: 240 4.8% 95.9%;
            --accent-foreground: 240 5.9% 10%;
            --destructive: 0 84.2% 60.2%;
            --destructive-foreground: 0 0% 98%;
            --border: 240 5.9% 90%;
            --input: 240 5.9% 90%;
            --ring: 152 76% 36%;
            --radius: 0.5rem;
            --sidebar-background: 0 0% 98%;
            --sidebar-foreground: 240 5.3% 26.1%;
            --sidebar-primary: 240 5.9% 10%;
            --sidebar-primary-foreground: 0 0% 98%;
            --sidebar-accent: 240 4.8% 95.9%;
            --sidebar-accent-foreground: 240 5.9% 10%;
            --sidebar-border: 220 13% 91%;
        }

        .dark {
            --background: 240 10% 3.9%;
            --foreground: 0 0% 98%;
            --card: 240 10% 3.9%;
            --card-foreground: 0 0% 98%;
            --popover: 240 10% 3.9%;
            --popover-foreground: 0 0% 98%;
            --primary: 152 76% 40%;
            --primary-foreground: 144 100% 11%;
            --secondary: 240 3.7% 15.9%;
            --secondary-foreground: 0 0% 98%;
            --muted: 240 3.7% 15.9%;
            --muted-foreground: 240 5% 64.9%;
            --accent: 240 3.7% 15.9%;
            --accent-foreground: 0 0% 98%;
            --destructive: 0 62.8% 30.6%;
            --destructive-foreground: 0 0% 98%;
            --border: 240 3.7% 15.9%;
            --input: 240 3.7% 15.9%;
            --ring: 240 4.9% 83.9%;
            --sidebar-background: 240 10% 3.9%;
            --sidebar-foreground: 240 4.8% 95.9%;
            --sidebar-primary: 0 0% 98%;
            --sidebar-primary-foreground: 240 5.9% 10%;
            --sidebar-accent: 240 3.7% 15.9%;
            --sidebar-accent-foreground: 240 4.8% 95.9%;
            --sidebar-border: 240 3.7% 15.9%;
        }

        /* Subtle scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: hsl(var(--border));
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: hsl(var(--muted-foreground));
        }
    </style>

    <!-- Theme Initializer -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- Lucide Icons & Chart.js -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @stack('styles')
</head>
<body class="h-full bg-background font-sans text-foreground antialiased selection:bg-primary selection:text-primary-foreground">
    <div class="relative flex min-h-screen">
        <!-- Backdrop Mobile -->
        <div id="sidebar-backdrop" onclick="toggleSidebar()" class="fixed inset-0 z-40 hidden bg-black/50 backdrop-blur-sm transition-opacity lg:hidden"></div>

        <!-- Sidebar (Shadcn Inset Sidebar Style) -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col border-r border-sidebar-border bg-sidebar transition-transform duration-200 ease-in-out lg:static lg:translate-x-0">
            <!-- Sidebar Header / Brand -->
            <div class="flex h-14 items-center justify-between border-b border-sidebar-border px-4">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 font-semibold text-sidebar-foreground">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary text-primary-foreground shadow-sm">
                        <i data-lucide="trash-2" class="h-4 w-4"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm font-semibold tracking-tight">Smart Waste</span>
                        <span class="text-[11px] text-muted-foreground">Admin Portal</span>
                    </div>
                </a>
                <button type="button" onclick="toggleSidebar()" class="inline-flex h-8 w-8 items-center justify-center rounded-md text-muted-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground lg:hidden">
                    <i data-lucide="x" class="h-4 w-4"></i>
                </button>
            </div>

            <!-- Sidebar Navigation -->
            <div class="flex-1 overflow-y-auto px-3 py-4">
                <div class="px-2 pb-2 text-[11px] font-medium tracking-wider text-muted-foreground uppercase">
                    Menu Utama
                </div>
                <nav class="space-y-1">
                    <!-- Menu 1: Dashboard -->
                    <a href="{{ route('admin.dashboard') }}"
                       class="group flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-semibold shadow-xs' : 'text-muted-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }}">
                        <i data-lucide="layout-dashboard" class="h-4 w-4 shrink-0 transition-transform group-hover:scale-105"></i>
                        <span>Dashboard</span>
                    </a>

                    <!-- Menu 2: Laporan -->
                    <a href="{{ route('admin.reports.index') }}"
                       class="group flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.reports.*') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-semibold shadow-xs' : 'text-muted-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }}">
                        <i data-lucide="file-text" class="h-4 w-4 shrink-0 transition-transform group-hover:scale-105"></i>
                        <span>Laporan Warga</span>
                    </a>

                    <!-- Menu 3: Pengguna -->
                    <a href="{{ route('admin.users.index') }}"
                       class="group flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-semibold shadow-xs' : 'text-muted-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }}">
                        <i data-lucide="users" class="h-4 w-4 shrink-0 transition-transform group-hover:scale-105"></i>
                        <span>Manajemen User</span>
                    </a>

                    <!-- Menu 4: API Docs -->
                    <a href="{{ url('/docs/api') }}" target="_blank"
                       class="group flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-muted-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground transition-colors">
                        <i data-lucide="book-open" class="h-4 w-4 shrink-0 transition-transform group-hover:scale-105"></i>
                        <span>Dokumentasi API</span>
                        <i data-lucide="external-link" class="h-3 w-3 ml-auto opacity-70"></i>
                    </a>
                </nav>
            </div>

            <!-- Sidebar Footer / User Info & Logout -->
            <div class="border-t border-sidebar-border p-3">
                <div class="flex items-center justify-between gap-2 rounded-md p-2 hover:bg-sidebar-accent/50">
                    <div class="flex items-center gap-2.5 overflow-hidden">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary font-semibold text-xs border border-border">
                            {{ strtoupper(substr(Auth::user()->name ?? 'AD', 0, 2)) }}
                        </div>
                        <div class="flex flex-1 flex-col overflow-hidden text-left leading-tight">
                            <span class="truncate text-xs font-medium text-sidebar-foreground">{{ Auth::user()->name ?? 'Administrator' }}</span>
                            <span class="truncate text-[11px] text-muted-foreground">{{ Auth::user()->email ?? 'admin@banksampah.id' }}</span>
                        </div>
                    </div>
                    <form action="{{ route('admin.logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" title="Keluar / Logout" class="inline-flex h-8 w-8 items-center justify-center rounded-md text-muted-foreground hover:bg-destructive/10 hover:text-destructive transition-colors">
                            <i data-lucide="log-out" class="h-4 w-4"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Wrapper -->
        <div class="flex flex-1 flex-col min-w-0">
            <!-- Navbar / Header (Shadcn Navbar Style) -->
            <header class="sticky top-0 z-30 flex h-14 items-center justify-between border-b border-border bg-background/95 px-4 backdrop-blur supports-[backdrop-filter]:bg-background/60">
                <div class="flex items-center gap-3">
                    <!-- Sidebar Toggle Button -->
                    <button type="button" onclick="toggleSidebar()" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-input bg-background text-foreground shadow-xs hover:bg-accent hover:text-accent-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                        <i data-lucide="panel-left" class="h-4 w-4"></i>
                    </button>

                    <!-- Breadcrumb Nav -->
                    <nav aria-label="Breadcrumb" class="hidden sm:flex items-center gap-2 text-sm text-muted-foreground">
                        <span class="hover:text-foreground transition-colors">Admin</span>
                        <i data-lucide="chevron-right" class="h-3.5 w-3.5"></i>
                        <span class="font-medium text-foreground">@yield('title', 'Dashboard')</span>
                    </nav>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center gap-3">
                    <!-- User Badge -->
                    <div class="flex items-center gap-2 px-2.5 py-1 rounded-md border border-input bg-card text-xs font-medium">
                        <span class="h-5 w-5 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-[10px]">A</span>
                        <span class="text-foreground hidden sm:inline-block">{{ Auth::user()->name ?? 'Admin' }}</span>
                    </div>

                    <!-- Theme Toggle -->
                    <button type="button" onclick="toggleTheme()" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-input bg-background text-foreground shadow-xs hover:bg-accent hover:text-accent-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" title="Ganti Tema">
                        <i data-lucide="sun" class="h-4 w-4 hidden dark:block"></i>
                        <i data-lucide="moon" class="h-4 w-4 block dark:hidden"></i>
                    </button>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 p-4 md:p-6 lg:p-8">
                <!-- Flash Alerts (Shadcn Alert Component) -->
                @if (session('success'))
                    <div class="mb-6 flex items-start gap-3 rounded-lg border border-emerald-500/30 bg-emerald-50 dark:bg-emerald-950/30 p-4 text-emerald-900 dark:text-emerald-200 shadow-xs">
                        <i data-lucide="check-circle" class="h-5 w-5 text-emerald-600 dark:text-emerald-400 mt-0.5 shrink-0"></i>
                        <div class="text-sm font-medium">{{ session('success') }}</div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 flex items-start gap-3 rounded-lg border border-destructive/30 bg-destructive/10 p-4 text-destructive shadow-xs">
                        <i data-lucide="alert-circle" class="h-5 w-5 text-destructive mt-0.5 shrink-0"></i>
                        <div class="text-sm font-medium">{{ session('error') }}</div>
                    </div>
                @endif

                <div class="w-full">
                    @yield('content')
                </div>
            </main>

            <!-- Footer (Clean Shadcn Footer) -->
            <footer class="border-t border-border bg-background py-4 px-6 text-xs text-muted-foreground">
                <div class="w-full flex flex-col sm:flex-row items-center justify-between gap-2">
                    <div>
                        &copy; {{ date('Y') }} <span class="font-medium text-foreground">Sistem Manajemen Sampah</span>. Seluruh hak cipta dilindungi.
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center rounded-md bg-secondary px-2 py-0.5 text-[11px] font-medium text-secondary-foreground border border-border">
                            Shadcn UI
                        </span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Init Lucide Icons
        lucide.createIcons();

        // Sidebar Toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            const isHidden = sidebar.classList.contains('-translate-x-full');
            
            if (isHidden) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            }
        }

        // Theme Toggle (Dark/Light Mode)
        function toggleTheme() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
        }
    </script>
    @stack('scripts')
</body>
</html>
