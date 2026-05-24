@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Dashboard Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-slate-900">Dashboard</h1>
        <div class="flex items-center space-x-3 mt-4 sm:mt-0">
            <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                <i class="fas fa-plus"></i>
                New Report
            </button>
            <button class="px-4 py-2 bg-slate-200 text-slate-800 rounded-lg hover:bg-slate-300 transition-colors flex items-center gap-2">
                <i class="fas fa-download"></i>
                Export Data
            </button>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Students Card -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">Total Students</p>
                    <p class="text-2xl font-bold text-slate-900">1,245</p>
                </div>
                <div class="bg-blue-50 p-3 rounded-lg">
                    <i class="fas fa-user-graduate text-blue-600 text-xl"></i>
                </div>
            </div>
            <div class="flex items-center space-x-3 text-sm">
                <div class="flex-1">
                    <p class="text-slate-500">This Month</p>
                    <p class="font-medium text-slate-900">+12%</p>
                </div>
                <div class="w-0.5 bg-slate-200"></div>
                <div>
                    <p class="text-slate-500">Active</p>
                    <p class="font-medium text-slate-900">1,180</p>
                </div>
            </div>
        </div>

        <!-- Teachers Card -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">Total Teachers</p>
                    <p class="text-2xl font-bold text-slate-900">85</p>
                </div>
                <div class="bg-green-50 p-3 rounded-lg">
                    <i class="fas fa-chalkboard-teacher text-green-600 text-xl"></i>
                </div>
            </div>
            <div class="flex items-center space-x-3 text-sm">
                <div class="flex-1">
                    <p class="text-slate-500">This Month</p>
                    <p class="font-medium text-slate-900">+3%</p>
                </div>
                <div class="w-0.5 bg-slate-200"></div>
                <div>
                    <p class="text-slate-500">Active</p>
                    <p class="font-medium text-slate-900">82</p>
                </div>
            </div>
        </div>

        <!-- Classes Card -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">Total Classes</p>
                    <p class="text-2xl font-bold text-slate-900">42</p>
                </div>
                <div class="bg-purple-50 p-3 rounded-lg">
                    <i class="fas fa-chalkboard text-purple-600 text-xl"></i>
                </div>
            </div>
            <div class="flex items-center space-x-3 text-sm">
                <div class="flex-1">
                    <p class="text-slate-500">This Month</p>
                    <p class="font-medium text-slate-900">+2</p>
                </div>
                <div class="w-0.5 bg-slate-200"></div>
                <div>
                    <p class="text-slate-500">Ongoing</p>
                    <p class="font-medium text-slate-900">38</p>
                </div>
            </div>
        </div>

        <!-- Attendance Card -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">Attendance Rate</p>
                    <p class="text-2xl font-bold text-slate-900">94.5%</p>
                </div>
                <div class="bg-orange-50 p-3 rounded-lg">
                    <i class="fas fa-check-square text-orange-600 text-xl"></i>
                </div>
            </div>
            <div class="flex items-center space-x-3 text-sm">
                <div class="flex-1">
                    <p class="text-slate-500">Today</p>
                    <p class="font-medium text-slate-900">+2.1%</p>
                </div>
                <div class="w-0.5 bg-slate-200"></div>
                <div>
                    <p class="text-slate-500">Target</p>
                    <p class="font-medium text-slate-900">95%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Recent Activity -->
    <div class="grid gap-6 sm:grid-cols-2">
        <!-- Charts Container -->
        <div class="col-span-1">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-slate-900 mb-4">Monthly Trends</h2>
                    <div class="h-96 bg-slate-50 rounded-xl">
                        <!-- Chart placeholder -->
                        <div class="flex h-full items-center justify-center text-slate-400">
                            Chart Placeholder
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="col-span-1">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-slate-900">Recent Activity</h2>
                        <a href="#" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                            View All
                        </a>
                    </div>
                    <div class="space-y-4">
                        <!-- Activity Item -->
                        <div class="flex items-start space-x-3">
                            <div class="bg-blue-50 p-2 rounded-lg flex-shrink-0">
                                <i class="fas fa-user-graduate text-blue-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-slate-900">New student enrolled: John Doe</p>
                                <p class="text-sm text-slate-500">2 minutes ago</p>
                            </div>
                        </div>
                        
                        <!-- Activity Item -->
                        <div class="flex items-start space-x-3">
                            <div class="bg-green-50 p-2 rounded-lg flex-shrink-0">
                                <i class="fas fa-check-square text-green-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-slate-900">Attendance updated for Grade 10</p>
                                <p class="text-sm text-slate-500">15 minutes ago</p>
                            </div>
                        </div>
                        
                        <!-- Activity Item -->
                        <div class="flex items-start space-x-3">
                            <div class="bg-purple-50 p-2 rounded-lg flex-shrink-0">
                                <i class="fas fa-calendar-alt text-purple-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-slate-900">New class schedule published</p>
                                <p class="text-sm text-slate-500">1 hour ago</p>
                            </div>
                        </div>
                        
                        <!-- Activity Item -->
                        <div class="flex items-start space-x-3">
                            <div class="bg-orange-50 p-2 rounded-lg flex-shrink-0">
                                <i class="fas fa-credit-card text-orange-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-slate-900">Payment received: $1,200</p>
                                <p class="text-sm text-slate-500">2 hours ago</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions and Upcoming Events -->
    <div class="grid gap-6 sm:grid-cols-2">
        <!-- Quick Actions -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
            <div class="p-6">
                <h2 class="text-xl font-bold text-slate-900 mb-4">Quick Actions</h2>
                <div class="space-y-3">
                    <button class="w-full flex items-center justify-start px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg hover:bg-slate-100 transition-colors text-left">
                        <i class="fas fa-user-plus mr-3 text-blue-600"></i>
                        <span>Add New Student</span>
                    </button>
                    <button class="w-full flex items-center justify-start px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg hover:bg-slate-100 transition-colors text-left">
                        <i class="fas fa-chalkboard-teacher mr-3 text-green-600"></i>
                        <span>Add New Teacher</span>
                    </button>
                    <button class="w-full flex items-center justify-start px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg hover:bg-slate-100 transition-colors text-left">
                        <i class="fas fa-calendar-plus mr-3 text-purple-600"></i>
                        <span>Create Class</span>
                    </button>
                    <button class="w-full flex items-center justify-start px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg hover:bg-slate-100 transition-colors text-left">
                        <i class="fas fa-clipboard-list mr-3 text-orange-600"></i>
                        <span>Take Attendance</span>
                    </button>
                    <button class="w-full flex items-center justify-start px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg hover:bg-slate-100 transition-colors text-left">
                        <i class="fas fa-file-alt mr-3 text-red-600"></i>
                        <span>Generate Report</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Upcoming Events -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
            <div class="p-6">
                <h2 class="text-xl font-bold text-slate-900 mb-4">Upcoming Events</h2>
                <div class="space-y-4">
                    <!-- Event Item -->
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-3 h-3 bg-blue-600 rounded-full"></div>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-slate-900">Parent-Teacher Meeting</p>
                            <p class="text-sm text-slate-500">Tomorrow, 10:00 AM</p>
                        </div>
                    </div>
                    
                    <!-- Event Item -->
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-3 h-3 bg-green-600 rounded-full"></div>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-slate-900">Science Fair</p>
                            <p class="text-sm text-slate-500">Next Friday</p>
                        </div>
                    </div>
                    
                    <!-- Event Item -->
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-3 h-3 bg-purple-600 rounded-full"></div>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-slate-900">Winter Break Starts</p>
                            <p class="text-sm text-slate-500">Dec 20</p>
                        </div>
                    </div>
                    
                    <!-- Event Item -->
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-3 h-3 bg-orange-600 rounded-full"></div>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-slate-900">Graduation Rehearsal</p>
                            <p class="text-sm text-slate-500">May 25</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection