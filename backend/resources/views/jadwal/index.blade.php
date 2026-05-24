@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-slate-900">Jadwal Pelajaran</h1>
        <div class="flex items-center space-x-3 mt-4 sm:mt-0">
            <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                <i class="fas fa-plus"></i>
                Buat Jadwal Baru
            </button>
            <button class="px-4 py-2 bg-slate-200 text-slate-800 rounded-lg hover:bg-slate-300 transition-colors flex items-center gap-2">
                <i class="fas fa-file-export"></i>
                Ekspor Jadwal
            </button>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="relative w-full sm:w-auto mb-4 sm:mb-0">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" placeholder="Cari mata pelajaran..." class="pl-10 pr-4 py-2 w-full bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
            </div>
            
            <div class="flex space-x-3">
                <select class="px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all min-w-[200px]">
                    <option value="">Semua Kelas</option>
                    <option value="">X</option>
                    <option value="">XI</option>
                    <option value="">XII</option>
                </select>
                <select class="px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all min-w-[200px]">
                    <option value="">Semua Hari</option>
                    <option value="">Senin</option>
                    <option value="">Selasa</option>
                    <option value="">Rabu</option>
                    <option value="">Kamis</option>
                    <option value="">Jumat</option>
                    <option value="">Sabtu</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Schedule Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Hari</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Jam</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Kelas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Mata Pelajaran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Guru</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Ruang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">Senin</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">07:00 - 08:30</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">X RPL 1</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">Matematika</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">Budi Santoso</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">Lab 1</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-blue-600 hover:text-blue-900 px-3 py-1 rounded hover:bg-blue-50">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">Selasa</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">08:30 - 10:00</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">XI RPL 2</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">Fisika</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">Dian Purnama</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">Lab 2</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-blue-600 hover:text-blue-900 px-3 py-1 rounded hover:bg-blue-50">
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