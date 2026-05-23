@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-slate-900">Guru</h1>
        <div class="flex items-center space-x-3 mt-4 sm:mt-0">
            <button class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center gap-2">
                <i class="fas fa-plus"></i>
                Tambah Guru Baru
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
                <input type="text" placeholder="Search teachers..." class="pl-10 pr-4 py-2 w-full bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all">
            </div>
            
            <div class="flex space-x-3">
                <select class="px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all min-w-[200px]">
                    <option value="">All Departments</option>
                    <option value="">Mathematics</option>
                    <option value="">Science</option>
                    <option value="">English</option>
                </select>
                <select class="px-4 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all min-w-[200px]">
                    <option value="">All Status</option>
                    <option value="">Active</option>
                    <option value="">On Leave</option>
                    <option value="">Retired</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Teachers Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Subject</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Phone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <!-- Teacher Row -->
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">T001</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">Sarah Wilson</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">Mathematics</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">sarah.wilson@email.com</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">+1 (555) 111-2222</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Active
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-green-600 hover:text-green-900 px-3 py-1 rounded hover:bg-green-50">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="ml-2 text-red-600 hover:text-red-900 px-3 py-1 rounded hover:bg-red-50">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    
                    <!-- Teacher Row -->
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">T002</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">David Chen</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">Science</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">david.chen@email.com</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">+1 (555) 333-4444</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Active
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-green-600 hover:text-green-900 px-3 py-1 rounded hover:bg-green-50">
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