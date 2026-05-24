@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-slate-900">Kehadiran</h1>
        <div class="flex items-center space-x-3 mt-4 sm:mt-0">
            <a href="{{ route('qr.scanner') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                <i class="fas fa-qrcode"></i>
                Scan QR
            </a>
            <button class="px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 transition-colors flex items-center gap-2">
                <i class="fas fa-plus"></i>
                Tambah Absensi
            </button>
            <a href="{{ route('export.kehadiran') }}" class="px-4 py-2 bg-slate-200 text-slate-800 rounded-lg hover:bg-slate-300 transition-colors flex items-center gap-2">
                <i class="fas fa-file-export"></i>
                Export CSV
            </a>
            <form action="{{ route('import.kehadiran') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2">
                @csrf
                <input type="file" name="file" accept=".csv" class="text-sm" required>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors text-sm">Import</button>
            </form>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="relative w-full sm:w-auto mb-4 sm:mb-0">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" placeholder="Cari siswa..." class="pl-10 pr-4 py-2 w-full bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-600 focus:border-slate-600 transition-all">
            </div>
            
            <div class="flex space-x-3">
                <select class="px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-600 focus:border-slate-600 transition-all min-w-[200px]">
                    <option value="">Semua Kelas</option>
                    <option value="">X</option>
                    <option value="">XI</option>
                    <option value="">XII</option>
                </select>
                <select class="px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-600 focus:border-slate-600 transition-all min-w-[200px]">
                    <option value="">Semua Status</option>
                    <option value="">Hadir</option>
                    <option value="">Izin</option>
                    <option value="">Sakit</option>
                    <option value="">Alfa</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Attendance Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">NIS</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Kelas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Keterangan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">12345</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">Ahmad Ramadhan</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">XII RPL 1</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">2024-01-15</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Hadir
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">-</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-slate-600 hover:text-slate-900 px-3 py-1 rounded hover:bg-slate-50">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">12346</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">Siti Nurhaliza</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">XI RPL 2</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">2024-01-15</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Izin
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">Sakit</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-slate-600 hover:text-slate-900 px-3 py-1 rounded hover:bg-slate-50">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection