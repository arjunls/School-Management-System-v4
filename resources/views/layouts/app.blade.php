<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Management System</title>
    <link rel="manifest" href="{{ url('/manifest.json') }}">
    <meta name="theme-color" content="#1e293b">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="SMK V4">
    <link rel="apple-touch-icon" href="{{ url('/icons/192') }}">
    <script src="https://cdn.tailwindcss.com" data-tailwind-config='{ "darkMode": "class" }'></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Custom styles for smooth transitions */
        .sidebar-transition {
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 300ms;
        }
    </style>
</head>
<body class="flex h-screen bg-gray-50">
    <!-- Sidebar -->
    <div class="sidebar-transition w-64 bg-slate-900 text-white flex-shrink-0">
        <div class="px-4 py-6 border-b border-slate-800">
            <h1 class="text-xl font-bold flex items-center gap-3">
                <i class="fas fa-school text-blue-400"></i>
                {{ __('common.app_name') }}
            </h1>
        </div>
        <nav class="mt-6 space-y-1">
            <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors sidebar-transition">
                <i class="fas fa-tachometer-alt mr-3"></i>
                {{ __('common.dashboard') }}
            </a>
            <a href="{{ route('siswa.index') }}" class="flex items-center px-4 py-3 text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors sidebar-transition">
                <i class="fas fa-user-graduate mr-3"></i>
                {{ __('common.siswa') }}
            </a>
            <a href="{{ route('guru.index') }}" class="flex items-center px-4 py-3 text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors sidebar-transition">
                <i class="fas fa-chalkboard-teacher mr-3"></i>
                {{ __('common.guru') }}
            </a>
            <a href="{{ route('kelas.index') }}" class="flex items-center px-4 py-3 text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors sidebar-transition">
                <i class="fas fa-chalkboard mr-3"></i>
                {{ __('common.kelas') }}
            </a>
            <a href="{{ route('mapel.index') }}" class="flex items-center px-4 py-3 text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors sidebar-transition">
                <i class="fas fa-book mr-3"></i>
                Mapel
            </a>
            <a href="{{ route('kehadiran.index') }}" class="flex items-center px-4 py-3 text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors sidebar-transition">
                <i class="fas fa-check-square mr-3"></i>
                {{ __('common.kehadiran') }}
            </a>
            <a href="{{ route('jadwal.index') }}" class="flex items-center px-4 py-3 text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors sidebar-transition">
                <i class="fas fa-calendar-alt mr-3"></i>
                {{ __('common.jadwal') }}
            </a>
            <a href="{{ route('nilai.index') }}" class="flex items-center px-4 py-3 text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors sidebar-transition">
                <i class="fas fa-list mr-3"></i>
                {{ __('common.nilai') }}
            </a>
            <a href="{{ route('pembayaran.index') }}" class="flex items-center px-4 py-3 text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors sidebar-transition">
                <i class="fas fa-credit-card mr-3"></i>
                {{ __('common.pembayaran') }}
            </a>
            <a href="{{ route('laporan.index') }}" class="flex items-center px-4 py-3 text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors sidebar-transition">
                <i class="fas fa-chart-bar mr-3"></i>
                {{ __('common.laporan') }}
            </a>
            <a href="{{ route('rapor.index') }}" class="flex items-center px-4 py-3 text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors sidebar-transition">
                <i class="fas fa-file-pdf mr-3"></i>
                Rapor Digital
            </a>
            <a href="{{ route('dokumen.index') }}" class="flex items-center px-4 py-3 text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors sidebar-transition">
                <i class="fas fa-file-alt mr-3"></i>
                {{ __('common.dokumen') }}
            </a>
            <a href="{{ route('activity.index') }}" class="flex items-center px-4 py-3 text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors sidebar-transition">
                <i class="fas fa-history mr-3"></i>
                Aktivitas
            </a>
        </nav>
        <div class="mt-auto border-t border-slate-800">
            <div class="px-4 py-4">
                <a href="#" class="flex items-center px-3 py-2 text-sm font-medium text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg transition-colors sidebar-transition">
                    <i class="fas fa-cog mr-3"></i>
                    {{ __('common.pengaturan') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
        <!-- Navbar -->
        <header class="bg-white border-b border-slate-200 flex-shrink-0">
            <div class="flex items-center justify-between px-6 py-4">
                <!-- Mobile Menu Button -->
                <button id="mobile-menu-button" class="md:hidden p-2 text-slate-600 hover:text-slate-900 rounded-lg hover:bg-slate-100 transition-colors">
                    <i class="fas fa-bars"></i>
                </button>
                
                <!-- Search -->
                <div class="flex-1 mx-6">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" placeholder="{{ __('common.search') }}" class="pl-10 pr-4 py-2 w-full bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    </div>
                </div>
                
                <!-- User Actions -->
                <div class="flex items-center space-x-4">
                    <!-- Notifications -->
                    <div class="relative" x-data="{ open: false, count: 0, items: [] }">
                        <button @click="open = !open; if(open) fetchNotifications()" class="p-2 text-slate-600 hover:text-slate-900 rounded-lg hover:bg-slate-100 transition-colors relative">
                            <i class="fas fa-bell"></i>
                            <span x-show="count > 0" x-text="count" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-medium"></span>
                        </button>
                        <!-- Dropdown -->
                        <div x-show="open" @click.outside="open = false" class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-slate-200 z-50">
                            <div class="p-3 border-b border-slate-200 flex items-center justify-between">
                                <span class="font-semibold text-slate-900">{{ __('common.notifikasi') }}</span>
                                <button @click="markAllRead()" class="text-xs text-blue-600 hover:text-blue-800">{{ __('common.mark_all_read') }}</button>
                            </div>
                            <div class="max-h-64 overflow-y-auto">
                                <template x-for="item in items" :key="item.id">
                                    <a href="#" class="block px-4 py-3 hover:bg-slate-50 border-b border-slate-100 last:border-0">
                                        <p class="text-sm text-slate-900" x-text="item.data.subject || '{{ __('common.notifikasi') }}'"></p>
                                        <p class="text-xs text-slate-500 mt-1" x-text="new Date(item.created_at).toLocaleDateString('id-ID')"></p>
                                    </a>
                                </template>
                                <template x-if="items.length === 0">
                                    <p class="text-sm text-slate-500 text-center py-6">{{ __('common.no_notifications') }}</p>
                                </template>
                            </div>
                        </div>
                        <script>
                            function fetchNotifications() {
                                fetch('/api/notifications/unread', { headers: { 'Authorization': 'Bearer ' + localStorage.getItem('token') } })
                                    .then(r => r.json())
                                    .then(d => {
                                        if (d.success) {
                                            document.querySelector('[x-data]').__x.$data.count = d.data.count;
                                            document.querySelector('[x-data]').__x.$data.items = d.data.notifications;
                                        }
                                    }).catch(() => {});
                            }
                        </script>
                    </div>
                    
                    <!-- Language Switcher -->
                    <div class="relative">
                        <button class="p-2 text-slate-600 hover:text-slate-900 rounded-lg hover:bg-slate-100 transition-colors text-sm font-medium">
                            {{ app()->getLocale() === 'id' ? 'ID' : 'EN' }}
                        </button>
                        <div class="absolute right-0 mt-2 w-24 bg-white rounded-xl shadow-lg border border-slate-200 z-50 hidden">
                            <div class="py-1">
                                <a href="{{ route('lang.switch', 'id') }}" class="block px-3 py-1.5 text-sm text-slate-700 hover:bg-slate-100 {{ app()->getLocale() === 'id' ? 'font-bold text-blue-600' : '' }}">Indonesia</a>
                                <a href="{{ route('lang.switch', 'en') }}" class="block px-3 py-1.5 text-sm text-slate-700 hover:bg-slate-100 {{ app()->getLocale() === 'en' ? 'font-bold text-blue-600' : '' }}">English</a>
                            </div>
                        </div>
                    </div>

                    <!-- Theme Toggle -->
                    <button id="theme-toggle" class="p-2 text-slate-600 hover:text-slate-900 rounded-lg hover:bg-slate-100 transition-colors">
                        <i class="fas fa-moon"></i>
                    </button>
                    
                    <!-- User Dropdown -->
                    <div class="relative">
                        <button class="flex items-center space-x-2 p-2 text-slate-600 hover:text-slate-900 rounded-lg hover:bg-slate-100 transition-colors">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 text-sm font-medium">
                                {{ substr(auth()->user()?->name ?? 'U', 0, 1) }}
                            </div>
                            <span class="hidden md:block font-medium">{{ auth()->user()?->name ?? 'User' }}</span>
                            <i class="fas fa-chevron-down ml-1 text-xs"></i>
                        </button>
                        <!-- Dropdown Menu -->
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-200 z-50 hidden">
                            <div class="py-2">
                                <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-100">{{ __('common.profil_saya') }}</a>
                                <a href="#" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-100">{{ __('common.pengaturan') }}</a>
                                <hr class="my-1 border-slate-200">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">{{ __('common.logout') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
    <script>
        // Mobile sidebar toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
document.querySelector('.bg-slate-900').classList.toggle('-translate-x-full');
        });

        // Theme toggle (simplified)
        document.getElementById('theme-toggle').addEventListener('click', function() {
            document.documentElement.classList.toggle('dark');
            // Update icon
            const icon = this.querySelector('i');
            if (document.documentElement.classList.contains('dark')) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            } else {
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
            }
        });

        // Dropdown toggle
        document.querySelectorAll('.relative > button').forEach(button => {
            button.addEventListener('click', function() {
                this.parentElement.querySelector('div').classList.toggle('hidden');
            });
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.relative')) {
                document.querySelectorAll('.relative > div').forEach(dropdown => {
                    dropdown.classList.add('hidden');
                });
            }
        });
    </script>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('{{ url("/serviceworker.js") }}');
            });
        }
    </script>
</body>
</html>