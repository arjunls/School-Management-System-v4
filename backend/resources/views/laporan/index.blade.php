@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-slate-900">Laporan</h1>
        <div class="flex items-center space-x-3 mt-4 sm:mt-0">
            <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                <i class="fas fa-plus"></i>
                Buat Laporan Baru
            </button>
            <button class="px-4 py-2 bg-slate-200 text-slate-800 rounded-lg hover:bg-slate-300 transition-colors flex items-center gap-2">
                <i class="fas fa-file-export"></i>
                Ekspor Laporan
            </button>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="relative w-full sm:w-auto mb-4 sm:mb-0">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" placeholder="Cari laporan..." class="pl-10 pr-4 py-2 w-full bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
            </div>
            
            <div class="flex space-x-3">
                <select class="px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all min-w-[200px]">
                    <option value="">Semua Tipe</option>
                    <option value="">Kehadiran</option>
                    <option value="">Nilai</option>
                    <option value="">Pembayaran</option>
                </select>
                <select class="px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all min-w-[200px]">
                    <option value="">Semua Bulan</option>
                    <option value="">Januari</option>
                    <option value="">Februari</option>
                    <option value="">Maret</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Reports Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Judul</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">RPT001</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">Laporan Kehadiran Januari</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">Kehadiran</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">2024-01-31</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-blue-600 hover:text-blue-900 px-3 py-1 rounded hover:bg-blue-50">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="ml-2 text-green-600 hover:text-green-900 px-3 py-1 rounded hover:bg-green-50">
                                <i class="fas fa-download"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection