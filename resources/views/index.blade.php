@extends('app')

@section('header', 'Uploaded IPCRF List')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h3 class="text-slate-800 font-bold text-lg">All Submissions</h3>
        <a href="{{ route('upload.create') }}" class="bg-blue-600 text-white hover:bg-white hover:text-blue-600 px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition-colors">
            <i data-lucide="plus" class="w-4 h-4"></i> Add New
        </a>    
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-200 flex gap-4">
            <div class="relative flex-1">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-5 h-5"></i>
                <input type="text" placeholder="Search records..." 
                    class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                />
            </div>
            <div class="flex gap-2">
                <select class="px-4 py-2 border border-slate-200 rounded-lg text-slate-600 bg-white focus:outline-none focus:border-blue-500">
                    <option value="">All Provinces</option>
                    <option value="Davao de Oro">Davao de Oro</option>
                    <option value="Davao del Norte">Davao del Norte</option>
                    <option value="Davao del Sur">Davao del Sur</option>
                    <option value="Davao Occidental">Davao Occidental</option>
                    <option value="Davao Oriental">Davao Oriental</option>
                </select>
                <button class="px-4 py-2 border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50 flex items-center gap-2">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    Export
                </button>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500 font-medium">
                    <tr>
                        <th class="px-6 py-4">ID</th>
                        <th class="px-6 py-4">Employee Name</th>
                        <th class="px-6 py-4">Location</th>
                        <th class="px-6 py-4">Date Uploaded</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($ipcrfs as $ipcrf)
                    <tr class="hover:bg-slate-50 transition-colors table-row" 
                        data-province="{{ $ipcrf->province }}" 
                        data-name="{{ $ipcrf->name }}" 
                        data-municipality="{{ $ipcrf->municipality }}"
                        data-status="{{ $ipcrf->status }}">
                        <td class="px-6 py-4 text-slate-500">#{{ str_pad($ipcrf->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-6 py-4 font-medium text-slate-900">{{ $ipcrf->name }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $ipcrf->municipality }}, {{ $ipcrf->province }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $ipcrf->created_at->format('M d, Y h:i A') }}</td>
                        <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium 
                            {{ $ipcrf->status === 'Pending' 
                                ? 'bg-orange-50 text-orange-700 border border-orange-100' 
                                : 'bg-green-50 text-green-700 border border-green-100' }}">
                            
                            <span class="w-1.5 h-1.5 rounded-full 
                                {{ $ipcrf->status === 'Pending' ? 'bg-orange-600' : 'bg-green-600' }}">
                            </span>
                            {{ $ipcrf->status }}
                        </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button class="text-slate-400 hover:text-red-600 transition-colors">
                                <i data-lucide="more-horizontal" class="w-5 h-5"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mb-3">
                                    <i data-lucide="inbox" class="w-6 h-6 text-slate-400"></i>
                                </div>
                                <p>No records found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="p-4 border-t border-slate-200">
            {{ $ipcrfs->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[placeholder="Search records..."]');
    const provinceFilter = document.querySelector('select');
    const exportButton = document.querySelector('button:has(i[data-lucide="download"])');
    const tableRows = document.querySelectorAll('.table-row');
    let dynamicEmptyState = null;

    // Search functionality
    function filterRecords() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedProvince = provinceFilter.value;
        let visibleCount = 0;

        tableRows.forEach(row => {
            const name = row.dataset.name.toLowerCase();
            const municipality = row.dataset.municipality.toLowerCase();
            const province = row.dataset.province;
            const status = row.dataset.status.toLowerCase();

            const matchesSearch = !searchTerm || 
                name.includes(searchTerm) || 
                municipality.includes(searchTerm) || 
                province.toLowerCase().includes(searchTerm) ||
                status.includes(searchTerm);

            const matchesProvince = !selectedProvince || province === selectedProvince;

            if (matchesSearch && matchesProvince) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Show/hide dynamic empty state
        const tbody = document.querySelector('tbody');
        
        // Remove any existing dynamic empty state
        if (dynamicEmptyState) {
            dynamicEmptyState.remove();
            dynamicEmptyState = null;
        }

        // Show empty state only if there are dynamic rows hidden and no visible results
        if (visibleCount === 0 && tableRows.length > 0) {
            const newEmptyState = document.createElement('tr');
            newEmptyState.className = 'dynamic-empty-state';
            newEmptyState.innerHTML = `
                <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                    <div class="flex flex-col items-center justify-center">
                        <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mb-3">
                            <i data-lucide="search" class="w-6 h-6 text-slate-400"></i>
                        </div>
                        <p>No records match your search</p>
                    </div>
                </td>
            `;
            tbody.appendChild(newEmptyState);
            dynamicEmptyState = newEmptyState;
            lucide.createIcons();
        }
    }

    // Event listeners
    searchInput.addEventListener('input', filterRecords);
    provinceFilter.addEventListener('change', filterRecords);

    // Export functionality
    exportButton.addEventListener('click', function() {
        const visibleRows = Array.from(tableRows).filter(row => row.style.display !== 'none');
        
        if (visibleRows.length === 0) {
            alert('No records to export');
            return;
        }

        // Create CSV content
        let csvContent = 'ID,Employee Name,Location,Date Uploaded,Status\n';
        
        visibleRows.forEach(row => {
            const cells = row.querySelectorAll('td');
            const id = cells[0].textContent.trim();
            const name = cells[1].textContent.trim();
            const location = cells[2].textContent.trim();
            const date = cells[3].textContent.trim();
            const status = cells[4].textContent.trim();
            
            csvContent += `"${id}","${name}","${location}","${date}","${status}"\n`;
        });

        // Download CSV
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', 'ipcrf_records.csv');
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });

    // Initialize Lucide icons
    lucide.createIcons();
});
</script>
@endpush
