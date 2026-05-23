@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-slate-900">Dokumen</h1>
        <div class="flex items-center space-x-3 mt-4 sm:mt-0">
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                <i class="fas fa-plus"></i>
                Tambah Dokumen
            </button>
            <button class="px-4 py-2 bg-slate-200 text-slate-800 rounded-lg hover:bg-slate-300 transition-colors flex items-center gap-2">
                <i class="fas fa-file-export"></i>
                Ekspor Data
            </button>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="relative w-full sm:w-auto mb-4 sm:mb-0">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" placeholder="Cari dokumen..." class="pl-10 pr-4 py-2 w-full bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
            </div>
            
            <div class="flex space-x-3">
                <select class="px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all min-w-[200px]">
                    <option value="">Semua Kategori</option>
                    <option value="">Ijazah</option>
                    <option value="">SKL</option>
                    <option value="">PKL</option>
                    <option value="">Raport</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Documents Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-505 uppercase tracking-wider">Nama Dokumen</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">NIS</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">DOC001</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">Raport Semester 1</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">Raport</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">12345</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">2024-01-15</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-indigo-600 hover:text-indigo-900 px-3 py-1 rounded hover:bg-indigo-50">
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