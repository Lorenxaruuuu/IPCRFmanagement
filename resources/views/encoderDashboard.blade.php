@extends('app')

@section('header', 'Encoder Dashboard')

@section('content')

@if(session('success'))
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
    {{ session('success') }}
</div>
@endif

<div class="space-y-8">
    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="file-text" class="w-6 h-6"></i>
                </div>
                <span class="text-xs font-medium px-2 py-1 rounded-full
                    {{ $growthPercentage >= 0 
                        ? 'bg-blue-50 text-blue-700' 
                        : 'bg-red-50 text-red-700' }}">
                    
                    {{ $growthPercentage >= 0 ? '+' : '' }}
                    {{ round($growthPercentage, 1) }}%
                </span>
            </div>
            <h3 class="text-3xl font-bold text-slate-800">{{ $totalUploaded ?? 0 }}</h3>
            <p class="text-slate-500 text-sm">Total IPCRF Uploaded</p>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="clock" class="w-6 h-6"></i>
                </div>
                <span class="text-xs font-medium bg-orange-50 text-orange-700 px-2 py-1 rounded-full">Pending</span>
            </div>
            <h3 class="text-3xl font-bold text-slate-800">{{ $pendingReview ?? 0 }}</h3>
            <p class="text-slate-500 text-sm">Pending Review</p>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-6 h-6"></i>
                </div>
                <span class="text-xs font-medium bg-emerald-50 text-emerald-700 px-2 py-1 rounded-full">Today</span>
            </div>
            <h3 class="text-3xl font-bold text-slate-800">{{ $completedToday ?? 0 }}</h3>
            <p class="text-slate-500 text-sm">Completed Today</p>
        </div>
    </div>

    <!-- Action Section -->
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-slate-800">Recent Uploads</h2>
        <a href="{{ route('upload.create') }}" 
        class="bg-blue-600 text-white px-5 py-2.5 rounded-lg font-medium flex items-center gap-2 shadow-lg shadow-blue-600/30 transition-all duration-200 
        hover:bg-white hover:text-blue-600 hover:shadow-5xl hover:-translate-y-0.5 active:scale-95">
            <i data-lucide="plus" class="w-5 h-5"></i>
            Upload Evaluated IPCRF
        </a>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-200 flex gap-4">
            <div class="relative flex-1">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-5 h-5"></i>
                <input 
                    type="text" 
                    placeholder="Search by name, province, or ID..." 
                    class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500"
                />
            </div>
            <button class="px-4 py-2 border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50 flex items-center gap-2">
                <i data-lucide="filter" class="w-4 h-4"></i>
                Filter
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 font-medium">
                    <tr>
                        <th class="px-6 py-4">Employee Name</th>
                        <th class="px-6 py-4">Location</th>
                        <th class="px-6 py-4">Date Uploaded</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentUploads ?? [] as $upload)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 font-medium text-slate-900">
                            {{ $upload->name }}
                        </td>
                        <td class="px-6 py-4 text-slate-500">
                            {{ $upload->municipality }}, {{ $upload->province }}
                        </td>
                        <td class="px-6 py-4 text-slate-500">
                            {{ $upload->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="bg-green-50 text-green-700">
                                {{ $upload->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-blue-600 hover:underline cursor-pointer">
                            View Details
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                            No uploads found. Start by uploading a new IPCRF.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
