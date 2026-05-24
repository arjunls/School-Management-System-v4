@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-slate-900">Kelas/Rombel</h1>
        <div class="flex items-center space-x-3 mt-4 sm:mt-0">
            <button class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center gap-2">
                <i class="fas fa-plus"></i>
                Tambah Kelas Baru
            </button>
            <button class="px-4 py-2 bg-slate-200 text-slate-800 rounded-lg hover:bg-slate-300 transition-colors flex items-center gap-2">
                <i class="fas fa-file-export"></i>
                Export
            </button>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="relative w-full sm:w-auto mb-4 sm:mb-0">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" placeholder="Search classes..." class="pl-10 pr-4 py-2 w-full bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all">
            </div>
            
            <div class="flex space-x-3">
                <select class="px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all min-w-[200px]">
                    <option value="">All Grades</option>
                    <option value="">Grade 1</option>
                    <option value="">Grade 2</option>
                    <option value="">Grade 3</option>
                </select>
                <select class="px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all min-w-[200px]">
                    <option value="">All Status</option>
                    <option value="">Active</option>
                    <option value="">Completed</option>
                    <option value="">Upcoming</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Classes Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Grade</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Teacher</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Schedule</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Students</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <!-- Class Row -->
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">CL001</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">Mathematics 101</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">Grade 10</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">Sarah Wilson</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">Mon/Wed/Fri 10:00-11:00</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">25</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Active
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-purple-600 hover:text-purple-900 px-3 py-1 rounded hover:bg-purple-50">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="ml-2 text-red-600 hover:text-red-900 px-3 py-1 rounded hover:bg-red-50">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    
                    <!-- Class Row -->
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">CL002</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">Science Lab</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">Grade 9</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">David Chen</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">Tue/Thu 14:00-15:30</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">22</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Active
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-purple-600 hover:text-purple-900 px-3 py-1 rounded hover:bg-purple-50">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="ml-2 text-red-600 hover:text-red-900 px-3 py-1 rounded hover:bg-red-50">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection