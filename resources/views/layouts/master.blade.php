<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Informasi Manajemen Sampah - Shadcn UI">
    <title>@yield('title', 'Manajemen Sampah')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- Google Fonts: Plus Jakarta Sans (Resmi DESIGN.md) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- TomSelect Searchable Dropdown CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/css/tom-select.default.min.css">

    <!-- Tailwind CSS (Play CDN for standalone instant render) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
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

    <!-- Shadcn Design Tokens: Single Fixed Light Canvas (DESIGN.md) -->
    <style>
        :root {
            --background: 210 40% 98%;      /* #F8FAFC Canvas */
            --foreground: 222 47% 11%;      /* #0F172A Text Main */
            --card: 0 0% 100%;              /* #FFFFFF Card Surface */
            --card-foreground: 222 47% 11%;
            --popover: 0 0% 100%;
            --popover-foreground: 222 47% 11%;
            --primary: 142 71% 29%;         /* #15803D Forest Green */
            --primary-foreground: 0 0% 100%;
            --secondary: 210 40% 96.1%;
            --secondary-foreground: 222 47% 11%;
            --muted: 210 40% 96.1%;
            --muted-foreground: 215 16% 47%;/* #64748B Slate 500 */
            --accent: 210 40% 96.1%;
            --accent-foreground: 142 71% 29%;
            --destructive: 0 84.2% 60.2%;
            --destructive-foreground: 0 0% 98%;
            --border: 214 32% 91%;          /* #E2E8F0 */
            --input: 214 32% 91%;
            --ring: 142 71% 29%;
            --radius: 0.5rem;
            --sidebar-background: 0 0% 100%;
            --sidebar-foreground: 222 47% 11%;
            --sidebar-primary: 142 71% 29%;
            --sidebar-primary-foreground: 0 0% 100%;
            --sidebar-accent: 210 40% 96.1%;
            --sidebar-accent-foreground: 142 71% 29%;
            --sidebar-border: 214 32% 91%;
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

        /* Skeleton Shimmer Loading (DESIGN.md) */
        .skeleton-shimmer {
            background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }
        @keyframes shimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* TomSelect Theme Customization for Shadcn - Synchronized with Search Input */
        .ts-wrapper {
            width: 100% !important;
            min-width: 0 !important;
            max-width: 100% !important;
            position: relative !important;
        }
        .ts-control {
            border: 1px solid hsl(var(--input)) !important;
            border-radius: 0.375rem !important; /* rounded-md */
            padding: 0.5rem 2rem 0.5rem 0.75rem !important; /* py-2 pl-3 pr-8 */
            font-size: 0.75rem !important; /* text-xs */
            background-color: hsl(var(--background)) !important; /* Match Search Input bg-background */
            color: hsl(var(--foreground)) !important;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important; /* shadow-xs */
            min-height: 2.25rem !important;
            height: 2.25rem !important;
            display: flex !important;
            align-items: center !important;
            width: 100% !important;
            min-width: 0 !important;
            max-width: 100% !important;
            overflow: hidden !important;
            white-space: nowrap !important;
            box-sizing: border-box !important;
            cursor: pointer !important;
            transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
        }
        .ts-control.focus,
        .ts-wrapper.focus .ts-control {
            border-color: #15803D !important;
            box-shadow: 0 0 0 1px #15803D !important; /* Match Search Input focus:ring-1 focus:ring-ring */
            outline: none !important;
        }
        .ts-wrapper.single .ts-control .item {
            background: transparent !important; /* Remove TomSelect default grey pill background */
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
            display: inline-block !important;
            max-width: 100% !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
            vertical-align: middle !important;
            font-size: 0.75rem !important;
            line-height: 1.25 !important;
            color: hsl(var(--foreground)) !important;
        }
        .ts-wrapper.single .ts-control input {
            font-size: 0.75rem !important;
            max-width: 100% !important;
            min-width: 0 !important;
            color: hsl(var(--foreground)) !important;
            background: transparent !important;
        }
        .ts-wrapper.single .ts-control input::placeholder {
            color: #64748B !important; /* Match placeholder:text-muted-foreground */
            font-size: 0.75rem !important;
        }
        .ts-dropdown {
            border: 1px solid hsl(var(--border)) !important;
            border-radius: 0.375rem !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1) !important;
            font-size: 0.75rem !important;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
            z-index: 50 !important;
            background-color: hsl(var(--card)) !important;
            color: hsl(var(--foreground)) !important;
            padding: 0.25rem 0 !important;
        }
        .ts-dropdown .option {
            padding: 0.375rem 0.75rem !important;
            font-size: 0.75rem !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
            max-width: 100% !important;
            color: hsl(var(--foreground)) !important;
            cursor: pointer !important;
        }
        .ts-dropdown .option.active,
        .ts-dropdown .option:hover {
            background-color: #DCFCE7 !important; /* Soft Emerald hover */
            color: #15803D !important;
            font-weight: 500 !important;
        }
        .ts-dropdown .option.selected {
            background-color: #F0FDF4 !important; /* Soft Emerald selected, not grey! */
            color: #15803D !important;
            font-weight: 600 !important;
        }
        .dark .ts-dropdown .option.active,
        .dark .ts-dropdown .option:hover {
            background-color: rgba(21, 128, 61, 0.2) !important;
            color: #4ADE80 !important;
        }
        .dark .ts-dropdown .option.selected {
            background-color: rgba(21, 128, 61, 0.3) !important;
            color: #86EFAC !important;
        }
        .ts-wrapper.single .ts-control:after {
            border-color: #64748B transparent transparent transparent !important;
            right: 0.75rem !important;
        }
        .ts-wrapper.single.dropdown-active .ts-control:after {
            border-color: transparent transparent #64748B transparent !important;
        }
        .dark .ts-wrapper.single .ts-control:after {
            border-color: #94A3B8 transparent transparent transparent !important;
        }
        .dark .ts-wrapper.single.dropdown-active .ts-control:after {
            border-color: transparent transparent #94A3B8 transparent !important;
        }

        /* Generic select ellipsis safeguard */
        select {
            text-overflow: ellipsis !important;
            overflow: hidden !important;
            white-space: nowrap !important;
            max-width: 100% !important;
        }
    </style>

    <!-- TomSelect JS -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/js/tom-select.complete.min.js"></script>

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
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col border-r border-sidebar-border bg-sidebar transition-transform duration-200 ease-in-out lg:sticky lg:top-0 lg:h-screen lg:shrink-0 lg:translate-x-0">
            <!-- Sidebar Header / Brand -->
            <div class="flex h-14 items-center justify-between border-b border-sidebar-border px-4">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 font-semibold text-sidebar-foreground">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-8 w-8 rounded-lg shadow-sm object-contain">
                    <div class="flex flex-col">
                        <span class="text-sm font-bold tracking-tight">Waste Management</span>
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
                       class="group flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-sidebar-accent text-primary font-semibold shadow-xs' : 'text-muted-foreground hover:bg-sidebar-accent hover:text-foreground' }}">
                        <i data-lucide="layout-dashboard" class="h-4 w-4 shrink-0 transition-transform group-hover:scale-105"></i>
                        <span>Dashboard</span>
                    </a>

                    <!-- Menu 2: Laporan -->
                    <a href="{{ route('admin.reports.index') }}"
                       class="group flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.reports.*') ? 'bg-sidebar-accent text-primary font-semibold shadow-xs' : 'text-muted-foreground hover:bg-sidebar-accent hover:text-foreground' }}">
                        <i data-lucide="file-text" class="h-4 w-4 shrink-0 transition-transform group-hover:scale-105"></i>
                        <span>{{ Auth::user()->hasRole('user') ? 'Laporan Saya' : 'Laporan Warga' }}</span>
                    </a>

                    @if (Auth::user()->hasRole('admin'))
                        <!-- Menu 3: Pengguna -->
                        <a href="{{ route('admin.users.index') }}"
                            class="group flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-sidebar-accent text-primary font-semibold shadow-xs' : 'text-muted-foreground hover:bg-sidebar-accent hover:text-foreground' }}">
                            <i data-lucide="users" class="h-4 w-4 shrink-0 transition-transform group-hover:scale-105"></i>
                            <span>Manajemen User</span>
                        </a>

                        <!-- Menu 4: Model AI Metrics (Internal Laravel Page) -->
                        <a href="{{ route('admin.ai.index') }}"
                            class="group flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.ai.*') ? 'bg-sidebar-accent text-primary font-semibold shadow-xs' : 'text-muted-foreground hover:bg-sidebar-accent hover:text-foreground' }}">
                            <i data-lucide="cpu" class="h-4 w-4 shrink-0 transition-transform group-hover:scale-105"></i>
                            <span>Evaluasi Model AI</span>
                        </a>
                    @endif

                    <div class="pt-4 px-2 pb-2 text-[11px] font-medium tracking-wider text-muted-foreground uppercase">
                        Pengaturan
                    </div>

                    <!-- Menu 5: Profil Saya -->
                    <a href="{{ route('admin.profile') }}"
                       class="group flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.profile*') ? 'bg-sidebar-accent text-primary font-semibold shadow-xs' : 'text-muted-foreground hover:bg-sidebar-accent hover:text-foreground' }}">
                        <i data-lucide="user-check" class="h-4 w-4 shrink-0 transition-transform group-hover:scale-105"></i>
                        <span>Profil Saya</span>
                    </a>

                    @if (Auth::user()->hasRole('admin'))
                        <!-- Menu 6: API Docs (Admin Only) -->
                        <a href="{{ url('/docs/api') }}" target="_blank"
                           class="group flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-muted-foreground hover:bg-sidebar-accent hover:text-foreground transition-colors">
                            <i data-lucide="book-open" class="h-4 w-4 shrink-0 transition-transform group-hover:scale-105"></i>
                            <span>Dokumentasi API</span>
                            <i data-lucide="external-link" class="h-3 w-3 ml-auto opacity-70"></i>
                        </a>
                    @endif
                </nav>
            </div>
        </aside>

        <!-- Main Wrapper -->
        <div class="flex flex-1 flex-col min-w-0">
            <!-- Navbar / Header (Shadcn Navbar Style) -->
            <header class="sticky top-0 z-30 flex h-14 items-center justify-between border-b border-border bg-card/95 px-4 backdrop-blur supports-[backdrop-filter]:bg-card/80">
                <div class="flex items-center gap-3">
                    <!-- Sidebar Toggle Button (Hanya tampil di HP/Tablet, tersembunyi di Laptop/Desktop) -->
                    <button type="button" onclick="toggleSidebar()" class="inline-flex lg:hidden h-9 w-9 items-center justify-center rounded-md border border-input bg-card text-foreground shadow-xs hover:bg-accent hover:text-accent-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                        <i data-lucide="panel-left" class="h-4 w-4"></i>
                    </button>

                    <!-- Breadcrumb Nav -->
                    <nav aria-label="Breadcrumb" class="hidden sm:flex items-center gap-2 text-sm text-muted-foreground">
                        <span class="hover:text-foreground transition-colors">Admin</span>
                        <i data-lucide="chevron-right" class="h-3.5 w-3.5"></i>
                        <span class="font-medium text-foreground">@yield('title', 'Dashboard')</span>
                    </nav>
                </div>

                <!-- Right Actions: User Profile Dropdown -->
                <div class="relative">
                    <button type="button" id="userMenuButton" onclick="toggleUserMenu()" class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg border border-input bg-card text-xs font-medium text-foreground hover:bg-accent transition-colors shadow-xs focus:outline-none">
                        <span class="h-6 w-6 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-xs border border-primary/20">
                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                        </span>
                        <div class="flex flex-col text-left hidden sm:flex">
                            <span class="font-semibold text-foreground text-xs leading-tight">{{ Auth::user()->name ?? 'User' }}</span>
                            <span class="text-[10px] text-muted-foreground capitalize leading-tight">{{ Auth::user()->roles->first()->name ?? 'Pengguna' }}</span>
                        </div>
                        <i data-lucide="chevron-down" class="h-3.5 w-3.5 text-muted-foreground ml-0.5 transition-transform duration-200" id="userMenuChevron"></i>
                    </button>

                    <!-- Dropdown Panel -->
                    <div id="userMenuDropdown" class="hidden absolute right-0 mt-2 w-56 rounded-xl border border-border bg-card shadow-lg py-1.5 z-50 transition-all">
                        <div class="px-3.5 py-2 border-b border-border">
                            <p class="text-xs font-semibold text-foreground truncate">{{ Auth::user()->name ?? 'Pengguna' }}</p>
                            <p class="text-[11px] text-muted-foreground truncate">{{ Auth::user()->email ?? '-' }}</p>
                        </div>
                        <div class="py-1">
                            <a href="{{ route('admin.profile') }}" class="flex items-center gap-2.5 px-3.5 py-2 text-xs text-foreground hover:bg-accent transition-colors">
                                <i data-lucide="user" class="h-4 w-4 text-muted-foreground"></i>
                                <span>Profil Saya</span>
                            </a>
                        </div>
                        <div class="border-t border-border pt-1">
                            <form action="{{ route('admin.logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2.5 px-3.5 py-2 text-xs text-destructive hover:bg-destructive/10 transition-colors text-left">
                                    <i data-lucide="log-out" class="h-4 w-4"></i>
                                    <span>Keluar (Logout)</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 p-4 md:p-6 lg:p-8 bg-background">
                <!-- Flash Alerts (Shadcn Alert Component) -->
                @if (session('success'))
                    <div class="mb-6 flex items-start gap-3 rounded-lg border border-emerald-500/30 bg-emerald-50 p-4 text-emerald-900 shadow-xs">
                        <i data-lucide="check-circle" class="h-5 w-5 text-primary mt-0.5 shrink-0"></i>
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

            <!-- Footer -->
            <footer class="border-t border-border bg-card py-4 px-6 text-xs text-muted-foreground">
                <div class="w-full flex flex-col sm:flex-row items-center justify-between gap-2">
                    <div>
                        &copy; {{ date('Y') }} <span class="font-semibold text-foreground">Waste Management</span>. Seluruh hak cipta dilindungi.
                    </div>
                    <div class="text-xs text-muted-foreground">
                        Sistem Pengelolaan Sampah Berkelanjutan
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

        // User Dropdown Toggle
        function toggleUserMenu() {
            const menu = document.getElementById('userMenuDropdown');
            const chevron = document.getElementById('userMenuChevron');
            if (!menu) return;
            const isClosed = menu.classList.contains('hidden');
            if (isClosed) {
                menu.classList.remove('hidden');
                chevron?.classList.add('rotate-180');
            } else {
                menu.classList.add('hidden');
                chevron?.classList.remove('rotate-180');
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function (e) {
            const button = document.getElementById('userMenuButton');
            const menu = document.getElementById('userMenuDropdown');
            if (button && menu && !button.contains(e.target) && !menu.contains(e.target)) {
                menu.classList.add('hidden');
                document.getElementById('userMenuChevron')?.classList.remove('rotate-180');
            }
        });

        // Initialize Searchable Dropdown (TomSelect) automatically on selects
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('select.tom-select, select.searchable-select').forEach(function (selectEl) {
                if (!selectEl.tomselect) {
                    new TomSelect(selectEl, {
                        create: false,
                        maxItems: 1,
                        placeholder: selectEl.getAttribute('placeholder') || 'Pilih atau cari...',
                        allowEmptyOption: true,
                        closeAfterSelect: true,
                        onItemAdd: function() {
                            this.control_input.blur();
                        }
                    });
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
