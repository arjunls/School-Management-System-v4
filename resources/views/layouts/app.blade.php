<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Management System</title>
    <script src="https://cdn.tailwindcss.com" data-tailwind-config='{ "darkMode": "class" }'></script>
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
                SchoolMS
            </h1>
        </div>
        <nav class="mt-6 space-y-1">
            <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors sidebar-transition">
                <i class="fas fa-tachometer-alt mr-3"></i>
                Dashboard
            </a>
            <a href="{{ route('siswa.index') }}" class="flex items-center px-4 py-3 text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors sidebar-transition">
                <i class="fas fa-user-graduate mr-3"></i>
                Siswa
            </a>
            <a href="{{ route('guru.index') }}" class="flex items-center px-4 py-3 text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors sidebar-transition">
                <i class="fas fa-chalkboard-teacher mr-3"></i>
                Guru
            </a>
            <a href="{{ route('kelas.index') }}" class="flex items-center px-4 py-3 text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors sidebar-transition">
                <i class="fas fa-chalkboard mr-3"></i>
                Kelas
            </a>
            <a href="{{ route('kehadiran.index') }}" class="flex items-center px-4 py-3 text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors sidebar-transition">
                <i class="fas fa-check-square mr-3"></i>
                Kehadiran
            </a>
            <a href="{{ route('jadwal.index') }}" class="flex items-center px-4 py-3 text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors sidebar-transition">
                <i class="fas fa-calendar-alt mr-3"></i>
                Jadwal
            </a>
            <a href="{{ route('nilai.index') }}" class="flex items-center px-4 py-3 text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors sidebar-transition">
                <i class="fas fa-list mr-3"></i>
                Nilai
            </a>
            <a href="{{ route('pembayaran.index') }}" class="flex items-center px-4 py-3 text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors sidebar-transition">
                <i class="fas fa-credit-card mr-3"></i>
                Pembayaran
            </a>
            <a href="{{ route('laporan.index') }}" class="flex items-center px-4 py-3 text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors sidebar-transition">
                <i class="fas fa-chart-bar mr-3"></i>
                Laporan
            </a>
            <a href="{{ route('dokumen.index') }}" class="flex items-center px-4 py-3 text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors sidebar-transition">
                <i class="fas fa-file-alt mr-3"></i>
                Dokumen
            </a>
        </nav>
        <div class="mt-auto border-t border-slate-800">
            <div class="px-4 py-4">
                <a href="#" class="flex items-center px-3 py-2 text-sm font-medium text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg transition-colors sidebar-transition">
                    <i class="fas fa-cog mr-3"></i>
                    Pengaturan
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
                        <input type="text" placeholder="Search..." class="pl-10 pr-4 py-2 w-full bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    </div>
                </div>
                
                <!-- User Actions -->
                <div class="flex items-center space-x-4">
                    <!-- Notifications -->
                    <div class="relative">
                        <button class="p-2 text-slate-600 hover:text-slate-900 rounded-lg hover:bg-slate-100 transition-colors">
                            <i class="fas fa-bell"></i>
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center"></span>
                        </button>
                    </div>
                    
                    <!-- Theme Toggle -->
                    <button id="theme-toggle" class="p-2 text-slate-600 hover:text-slate-900 rounded-lg hover:bg-slate-100 transition-colors">
                        <i class="fas fa-moon"></i>
                    </button>
                    
                    <!-- User Dropdown -->
                    <div class="relative">
                        <button class="flex items-center space-x-2 p-2 text-slate-600 hover:text-slate-900 rounded-lg hover:bg-slate-100 transition-colors">
                            <img src="https://via.placeholder.com/32" alt="User" class="w-8 h-8 rounded-full">
                            <span class="hidden md:block font-medium">Admin User</span>
                            <i class="fas fa-chevron-down ml-1 text-xs"></i>
                        </button>
                        <!-- Dropdown Menu -->
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-200 z-50 hidden">
                            <div class="py-2">
                                <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-100">Profil Saya</a>
                                <a href="#" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-100">Pengaturan</a>
                                <hr class="my-1 border-slate-200">
                                <a href="#" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">Logout</a>
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
</body>
</html>