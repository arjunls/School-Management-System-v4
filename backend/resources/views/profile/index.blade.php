@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900">Profil Saya</h1>
    </div>

    <!-- Profile Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6">
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                <!-- Avatar -->
                <div class="relative">
                    <div class="w-24 h-24 rounded-full bg-slate-100 border-4 border-slate-200 overflow-hidden">
                        <img src="{{ Auth::user()->photo ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=2563eb&color=fff' }}" 
                             alt="Avatar" class="w-full h-full object-cover">
                    </div>
                    <button class="absolute bottom-0 right-0 bg-blue-600 text-white p-2 rounded-full hover:bg-blue-700 transition-colors shadow-sm">
                        <i class="fas fa-camera text-xs"></i>
                    </button>
                </div>

                <!-- Info -->
                <div class="flex-1 text-center sm:text-left">
                    <h2 class="text-xl font-bold text-slate-900">{{ Auth::user()->name }}</h2>
                    <p class="text-sm text-slate-500">{{ Auth::user()->email }}</p>
                    <span class="inline-flex mt-2 px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                        {{ ucfirst(Auth::user()->role ?? 'User') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Profile Form -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="p-6">
            <h3 class="text-lg font-bold text-slate-900 mb-6">Edit Profil</h3>
            
            <form class="space-y-6">
                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Nama Lengkap</label>
                        <input type="text" value="{{ Auth::user()->name }}" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                        <input type="email" value="{{ Auth::user()->email }}" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Nomor Telepon</label>
                        <input type="text" value="{{ Auth::user()->phone ?? '' }}" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Jenis Kelamin</label>
                        <select class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                            <option value="male" {{ Auth::user()->gender == 'male' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="female" {{ Auth::user()->gender == 'female' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Tanggal Lahir</label>
                        <input type="date" value="{{ Auth::user()->date_of_birth ? Auth::user()->date_of_birth->format('Y-m-d') : '' }}" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Alamat</label>
                        <input type="text" value="{{ Auth::user()->address ?? '' }}" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Change Password -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="p-6">
            <h3 class="text-lg font-bold text-slate-900 mb-6">Ubah Password</h3>
            
            <form class="space-y-6">
                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Password Saat Ini</label>
                        <input type="password" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    </div>
                    <div></div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Password Baru</label>
                        <input type="password" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Konfirmasi Password Baru</label>
                        <input type="password" class="w-full px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection