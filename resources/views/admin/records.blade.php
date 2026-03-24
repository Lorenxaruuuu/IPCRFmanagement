@extends('admin.layouts.admin')

@section('title', 'IPCRF Records')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IPCRF Records</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #8b5cf6;
            --accent: #a855f7;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #06b6d4;
            --dark: #1e1b4b;
            --darker: #0f0a1e;
            --card-bg: rgba(30, 27, 75, 0.6);
            --glass: rgba(255, 255, 255, 0.05);
            --border: rgba(139, 92, 246, 0.2);
            --text: #e0e7ff;
            --text-muted: #94a3b8;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--darker) 0%, var(--dark) 100%);
            min-height: 100vh;
            color: var(--text);
            overflow-x: hidden;
        }

        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .bg-animation::before {
            content: '';
            position: absolute;
            width: 150%;
            height: 150%;
            background: radial-gradient(circle at 20% 80%, rgba(99, 102, 241, 0.15) 0%, transparent 50%),
                        radial-gradient(circle at 80% 20%, rgba(168, 85, 247, 0.15) 0%, transparent 50%);
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            50% { transform: translate(-30px, -30px) rotate(5deg); }
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 20px;
            background: var(--glass);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border);
            border-radius: 16px;
        }

        .back-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            color: var(--text);
        }

        .page-title h1 {
            font-size: 24px;
            font-weight: 700;
            background: linear-gradient(135deg, #fff, #c7d2fe);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .page-title p {
            color: var(--text-muted);
            font-size: 14px;
            margin-top: 4px;
        }

        .filter-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-label {
            font-size: 13px;
            font-weight: 500;
            color: var(--text-muted);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-select {
            padding: 14px 18px;
            background: var(--glass);
            border: 1px solid var(--border);
            border-radius: 12px;
            color: var(--text);
            font-size: 14px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .form-select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .form-select option {
            background: var(--dark);
            color: var(--text);
        }

        .records-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            padding: 16px;
            text-align: left;
            font-weight: 600;
            color: var(--text);
            border-bottom: 1px solid var(--border);
            background: rgba(99, 102, 241, 0.1);
        }

        td {
            padding: 16px;
            border-bottom: 1px solid var(--border);
            color: var(--text);
        }

        tr:hover {
            background: rgba(99, 102, 241, 0.05);
        }

        .btn-download {
            padding: 8px 16px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-download:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4);
        }

        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-saved {
            background: rgba(34, 197, 94, 0.2);
            color: #86efac;
        }

        .badge-pending {
            background: rgba(251, 191, 36, 0.2);
            color: #fcd34d;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state i {
            font-size: 48px;
            color: var(--text-muted);
            margin-bottom: 20px;
        }

        .empty-state h3 {
            color: var(--text);
            margin-bottom: 10px;
        }

        .empty-state p {
            color: var(--text-muted);
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .page-link {
            padding: 8px 12px;
            background: var(--glass);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .page-link:hover,
        .page-link.active {
            background: var(--primary);
            border-color: var(--primary);
        }

        @media (max-width: 768px) {
            .filter-grid {
                grid-template-columns: 1fr;
            }

            table {
                font-size: 12px;
            }

            th, td {
                padding: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="bg-animation"></div>
    
    <div class="container">
        <header class="header">
            <a href="{{ route('admin.dashboard') }}" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                Back to Dashboard
            </a>
            <div class="page-title" style="text-align: right;">
                <h1>IPCRF Records</h1>
                <p>View and manage uploaded IPCRF forms</p>
            </div>
        </header>

        <!-- Filter Section -->
        <div class="filter-card">
            <h3 style="margin-bottom: 20px; color: #fff;">Filter Records</h3>
            <form method="GET" class="filter-grid">
                <div class="form-group">
                    <label class="form-label">Employee ID</label>
                    <input type="text" name="employee_id" class="form-select" placeholder="Search by Employee ID" value="{{ request('employee_id') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Province</label>
                    <select name="province" class="form-select" onchange="loadMunicipalities(this.value)">
                        <option value="">All Provinces</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province->id }}" {{ request('province') == $province->id ? 'selected' : '' }}>
                                {{ $province->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Municipality</label>
                    <select name="municipality" class="form-select" id="municipality-select">
                        <option value="">All Municipalities</option>
                        @if(request('province'))
                            @foreach(\App\Models\Municipality::where('province_id', request('province'))->get() as $mun)
                                <option value="{{ $mun->id }}" {{ request('municipality') == $mun->id ? 'selected' : '' }}>
                                    {{ $mun->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div style="grid-column: 1 / -1;">
                    <button type="submit" style="padding: 12px 30px; background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                        <i class="fas fa-search"></i> Filter Records
                    </button>
                </div>
            </form>
        </div>

        <!-- Records Table -->
        <div class="records-card">
            @if($records->count() > 0)
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Employee ID - Name</th>
                                <th>Province</th>
                                <th>Municipality</th>
                                <th>School</th>
                                <th>Semester</th>
                                <th>Year</th>
                                <th>Status</th>
                                <th>Upload Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $record)
                                <tr>
                                    <td>{{ ($record->employee?->employee_id ?? 'N/A') . ' - ' . ($record->employee?->first_name ?? '') . ' ' . ($record->employee?->last_name ?? '') }}</td>
                                    <td>{{ $record->employee->school->municipality->province->name ?? 'N/A' }}</td>
                                    <td>{{ $record->employee->school->municipality->name ?? 'N/A' }}</td>
                                    <td>{{ $record->employee->school->name ?? 'N/A' }}</td>
                                    <td>{{ $record->semester }}</td>
                                    <td>{{ $record->school_year }}</td>
                                    <td>
                                        <span class="badge badge-{{ strtolower($record->status) }}">
                                            {{ $record->status }}
                                        </span>
                                    </td>
                                    <td>{{ $record->uploaded_at?->format('M d, Y') ?? 'N/A' }}</td>
                                    <td>
                                        @if($record->id)
                                            <a href="{{ route('admin.records.download', $record->id) }}" class="btn-download">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                        @else
                                            <span class="text-gray-400">–</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($records->hasPages())
                    <div class="pagination">
                        {{ $records->render() }}
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>No Records Found</h3>
                    <p>Try adjusting your filters or upload new IPCRF forms.</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        function loadMunicipalities(provinceId) {
            if (!provinceId) {
                document.getElementById('municipality-select').innerHTML = '<option value="">All Municipalities</option>';
                return;
            }
            
            fetch(`/admin/api/provinces/${provinceId}/municipalities`)
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('municipality-select');
                    select.innerHTML = '<option value="">All Municipalities</option>';
                    data.forEach(mun => {
                        select.innerHTML += `<option value="${mun.id}">${mun.name}</option>`;
                    });
                });
        }
    </script>
</body>
</html>
@endsection
