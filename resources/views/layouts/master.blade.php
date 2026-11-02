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
            --primary: 240 5.9% 10%;
            --primary-foreground: 0 0% 98%;
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
            --ring: 240 5.9% 10%;
            --radius: 0.5rem;
            --sidebar-background: 0 0% 98%;
            --sidebar-foreground: 240 5.3% 26.1%;
            --sidebar-primary: 240 5.9% 10%;
            --sidebar-primary-foreground: 0 0% 98%;
            --sidebar-accent: 240 4.8% 95.9%;
            --sidebar-accent-foreground: 240 5.9% 10%;
            --sidebar-border: 240 5.9% 90%;
        }

        .dark {
            --background: 240 10% 3.9%;
            --foreground: 0 0% 98%;
            --card: 240 10% 3.9%;
            --card-foreground: 0 0% 98%;
            --popover: 240 10% 3.9%;
            --popover-foreground: 0 0% 98%;
            --primary: 0 0% 98%;
            --primary-foreground: 240 5.9% 10%;
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

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

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
                <a href="{{ url('/') }}" class="flex items-center gap-2.5 font-semibold text-sidebar-foreground">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary text-primary-foreground shadow-sm">
                        <i data-lucide="trash-2" class="h-4 w-4"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm font-semibold tracking-tight">Manajemen Sampah</span>
                        <span class="text-[11px] text-muted-foreground">Admin Portal</span>
                    </div>
                </a>
                <button type="button" onclick="toggleSidebar()" class="inline-flex h-8 w-8 items-center justify-center rounded-md text-muted-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground lg:hidden">
                    <i data-lucide="x" class="h-4 w-4"></i>
                </button>
            </div>

            <!-- Sidebar Navigation (Tepat 3 Menu Saja) -->
            <div class="flex-1 overflow-y-auto px-3 py-4">
                <div class="px-2 pb-2 text-[11px] font-medium tracking-wider text-muted-foreground uppercase">
                    Menu Utama
                </div>
                <nav class="space-y-1">
                    <!-- Menu 1: Dashboard -->
                    <a href="{{ url('/') }}"
                       class="group flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors {{ request()->is('/') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-semibold shadow-xs' : 'text-muted-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }}">
                        <i data-lucide="layout-dashboard" class="h-4 w-4 shrink-0 transition-transform group-hover:scale-105"></i>
                        <span>Dashboard</span>
                    </a>

                    <!-- Menu 2: Data Sampah -->
                    <a href="#"
                       class="group flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors {{ request()->is('sampah*') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-semibold shadow-xs' : 'text-muted-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }}">
                        <i data-lucide="layers" class="h-4 w-4 shrink-0 transition-transform group-hover:scale-105"></i>
                        <span>Data Sampah</span>
                    </a>

                    <!-- Menu 3: Laporan -->
                    <a href="{{ route('laporan.index') }}"
                       class="group flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors {{ request()->is('laporan*') ? 'bg-sidebar-accent text-sidebar-accent-foreground font-semibold shadow-xs' : 'text-muted-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }}">
                        <i data-lucide="file-text" class="h-4 w-4 shrink-0 transition-transform group-hover:scale-105"></i>
                        <span>Laporan</span>
                    </a>
                </nav>
            </div>

            <!-- Sidebar Footer / User Info -->
            <div class="border-t border-sidebar-border p-3">
                <div class="flex items-center gap-3 rounded-md p-2 hover:bg-sidebar-accent">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-secondary font-semibold text-xs text-secondary-foreground border border-border">
                        AD
                    </div>
                    <div class="flex flex-1 flex-col overflow-hidden text-left leading-tight">
                        <span class="truncate text-xs font-medium text-sidebar-foreground">Administrator</span>
                        <span class="truncate text-[11px] text-muted-foreground">admin@banksampah.id</span>
                    </div>
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
                        <span class="hover:text-foreground transition-colors">Sistem</span>
                        <i data-lucide="chevron-right" class="h-3.5 w-3.5"></i>
                        <span class="font-medium text-foreground">@yield('title', 'Dashboard')</span>
                    </nav>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center gap-2">
                    <!-- Theme Toggle -->
                    <button type="button" onclick="toggleTheme()" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-input bg-background text-foreground shadow-xs hover:bg-accent hover:text-accent-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" title="Ganti Tema">
                        <i data-lucide="sun" class="h-4 w-4 hidden dark:block"></i>
                        <i data-lucide="moon" class="h-4 w-4 block dark:hidden"></i>
                    </button>

                    <!-- User Dropdown Toggle -->
                    <div class="relative">
                        <button type="button" onclick="toggleDropdown('user-dropdown')" class="flex h-9 items-center gap-2 rounded-md border border-input bg-background px-2.5 text-sm font-medium shadow-xs hover:bg-accent hover:text-accent-foreground">
                            <span class="h-6 w-6 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs font-semibold">A</span>
                            <span class="hidden md:inline-block">Admin</span>
                            <i data-lucide="chevron-down" class="h-3.5 w-3.5 text-muted-foreground"></i>
                        </button>

                        <!-- Minimal Dropdown Menu -->
                        <div id="user-dropdown" class="absolute right-0 mt-1 hidden w-48 rounded-md border border-border bg-popover p-1 text-popover-foreground shadow-md z-50">
                            <div class="px-2 py-1.5 text-xs font-semibold text-muted-foreground">Akun Saya</div>
                            <div class="h-px bg-border my-1"></div>
                            <a href="#" class="flex items-center gap-2 rounded-sm px-2 py-1.5 text-xs hover:bg-accent hover:text-accent-foreground transition-colors">
                                <i data-lucide="user" class="h-3.5 w-3.5"></i> Profil
                            </a>
                            <a href="#" class="flex items-center gap-2 rounded-sm px-2 py-1.5 text-xs hover:bg-accent hover:text-accent-foreground transition-colors">
                                <i data-lucide="settings" class="h-3.5 w-3.5"></i> Pengaturan
                            </a>
                            <div class="h-px bg-border my-1"></div>
                            <a href="#" class="flex items-center gap-2 rounded-sm px-2 py-1.5 text-xs text-destructive hover:bg-destructive/10 transition-colors">
                                <i data-lucide="log-out" class="h-3.5 w-3.5"></i> Keluar
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 p-4 md:p-6 lg:p-8">
                <div class="mx-auto max-w-7xl">
                    @yield('content')
                </div>
            </main>

            <!-- Footer (Clean Shadcn Footer) -->
            <footer class="border-t border-border bg-background py-4 px-6 text-xs text-muted-foreground">
                <div class="mx-auto max-w-7xl flex flex-col sm:flex-row items-center justify-between gap-2">
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

        // Theme Toggle (Light / Dark)
        function toggleTheme() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
        }

        // Dropdown Toggle
        function toggleDropdown(id) {
            const dropdown = document.getElementById(id);
            dropdown.classList.toggle('hidden');
        }

        // Close dropdown when clicked outside
        document.addEventListener('click', (e) => {
            const userDropdown = document.getElementById('user-dropdown');
            if (userDropdown && !userDropdown.contains(e.target) && !e.target.closest('button[onclick*="user-dropdown"]')) {
                userDropdown.classList.add('hidden');
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
