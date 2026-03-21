@extends('app')

@section('header', 'IPCRF Details')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <!-- Header Actions -->
    <div class="flex items-center justify-between">
        <a href="{{ route('dashboard') }}" class="text-slate-500 hover:text-slate-700 flex items-center gap-2 transition-colors">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
            Back to Dashboard
        </a>
        <div class="flex gap-3">
            <button class="px-4 py-2 bg-blue-50 text-blue-600 rounded-lg font-medium hover:bg-blue-100 transition-colors flex items-center gap-2">
                <i data-lucide="printer" class="w-4 h-4"></i>
                Print
            </button>
        </div>
    </div>

    <!-- Details Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-bold text-slate-800">{{ $ipcrf->name }}</h2>
                <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-50 text-green-700 border border-green-200">
                    {{ $ipcrf->status }}
                </span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-500 shrink-0">
                        <i data-lucide="map-pin" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500 mb-0.5">Location</p>
                        <p class="font-medium text-slate-700">{{ $ipcrf->municipality }}, {{ $ipcrf->province }}</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-500 shrink-0">
                        <i data-lucide="calendar" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500 mb-0.5">Date Uploaded</p>
                        <p class="font-medium text-slate-700">{{ $ipcrf->created_at->format('F d, Y h:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Document Preview Section -->
        <div class="p-6 bg-slate-50">
            <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                <i data-lucide="file" class="w-5 h-5 text-slate-400"></i>
                Scanned Document
            </h3>
            <div class="bg-white rounded-xl border border-slate-200 p-8 flex flex-col items-center justify-center min-h-[400px]">
                @if(Str::endsWith(strtolower($ipcrf->scanned_file_path), ['.pdf']))
                    <div class="w-full mb-6">
                        <iframe src="{{ route('ipcrf.document', $ipcrf->id) }}" class="w-full h-[600px] rounded-lg border border-slate-200 shadow-sm" type="application/pdf">
                            <p class="text-slate-600 mb-6 text-center max-w-sm">Your browser does not support PDFs. <a href="{{ route('ipcrf.document', $ipcrf->id) }}" target="_blank" class="text-blue-600 hover:underline">Download the PDF</a>.</p>
                        </iframe>
                    </div>
                    <div class="flex gap-4">
                        <a href="{{ route('ipcrf.document', $ipcrf->id) }}" target="_blank" class="px-6 py-2.5 bg-slate-800 text-white rounded-lg font-medium hover:bg-slate-900 transition-colors flex items-center gap-2 shadow-sm font-semibold">
                            <i data-lucide="external-link" class="w-4 h-4"></i>
                            Open PDF
                        </a>
                        <a href="{{ route('ipcrf.document', $ipcrf->id) }}" download class="px-6 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-lg font-medium hover:bg-slate-50 transition-colors flex items-center gap-2 shadow-sm font-semibold">
                            <i data-lucide="download" class="w-4 h-4"></i>
                            Download
                        </a>
                    </div>
                @else
                    <div class="w-full max-w-3xl overflow-hidden rounded-lg shadow-sm border border-slate-200 mb-6 bg-slate-100 flex items-center justify-center min-h-[300px]">
                        <img src="{{ route('ipcrf.document', $ipcrf->id) }}" alt="Scanned Document" class="max-w-full h-auto max-h-[600px] object-contain">
                    </div>
                    <a href="{{ route('ipcrf.document', $ipcrf->id) }}" download class="px-6 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-lg font-medium hover:bg-slate-50 transition-colors flex items-center gap-2 shadow-sm font-semibold">
                        <i data-lucide="download" class="w-4 h-4"></i>
                        Download Image
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
