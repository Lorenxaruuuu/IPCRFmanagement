<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <title>Performance History - DSWD Purchase Request Tracking System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

      
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #e8f4f8 0%, #d4e5ed 50%, #f0e6f0 100%);
            min-height: 100vh;
            display: flex;
        }

        /* Sidebar */
        .sidebar {
            width: 70px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px 0;
            gap: 25px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
            overflow: visible;
        }

        .nav-item {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            color: #999;
            position: relative;
        }

        .nav-item:hover {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
        }

        .nav-item.active {
            background: #667eea;
            color: white;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .nav-item svg {
            width: 22px;
            height: 22px;
        }

        /* logout modal */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }
        .modal {
            background: white;
            padding: 20px 30px;
            border-radius: 10px;
            max-width: 320px;
            text-align: center;
        }
        .modal button {
            margin: 10px 5px 0;
            padding: 8px 16px;
            border:none;
            border-radius:6px;
            cursor:pointer;
        }
        .btn-confirm { background:#1a73e8; color:#fff; }
        .btn-cancel { background:#bbb; color:#333; }
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #1a73e8;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
            display: inline-block;
            vertical-align: middle;
            margin-left:8px;
            visibility:hidden;
        }
        @keyframes spin { 100% { transform: rotate(360deg); } }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 40px;
        }

        .page-title {
            font-size: 32px;
            font-weight: 700;
            color: #333;
            margin-bottom: 30px;
            letter-spacing: 0.5px;
        }

        /* Card Container */
        .card-container {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        /* Card Header with Gradient */
        .card-header {
            height: 80px;
            background: linear-gradient(90deg, #a8d0e6 0%, #f5e6d3 100%);
        }

        /* Card Body */
        .card-body {
            background: white;
            padding: 30px;
        }

        /* Filter Section */
        .filter-section {
            display: flex;
            align-items: end;
            gap: 20px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filter-label {
            font-size: 11px;
            font-weight: 700;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .filter-select {
            width: 160px;
            padding: 10px 14px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            color: #333;
            background: white;
            cursor: pointer;
            outline: none;
        }

        .filter-select:focus {
            border-color: #1a73e8;
        }

        /* Download Button */
        .download-report-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: #0a1f44;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.2s;
            height: fit-content;
        }

        .download-report-btn:hover {
            background: #1a3a6e;
        }

        .download-icon {
            width: 14px;
            height: 14px;
        }

        /* Table Container */
        .table-container {
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table thead {
            background: white;
        }

        .data-table th {
            padding: 16px 20px;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            color: #333;
            border-bottom: 1px solid #e0e0e0;
            text-transform: capitalize;
        }

        .data-table td {
            padding: 16px 20px;
            font-size: 14px;
            color: #333;
            border-bottom: 1px solid #f0f0f0;
        }

        .data-table tr:hover {
            background-color: #fafafa;
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }

        /* View Link */
        .view-link {
            color: #1a73e8;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        .view-link:hover {
            text-decoration: underline;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px;
            color: #888;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                padding: 20px;
            }

            .filter-section {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-select {
                width: 100%;
            }

            .data-table th,
            .data-table td {
                padding: 12px 16px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
   <aside class="sidebar">
        <a href="{{ url('/home') }}" class="nav-item "  title="Home">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
        </a>
        <a href="{{ url('/forms') }}" class="nav-item" title="Dashboard">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                </svg>
        </a>
           <a href="{{ url('/performance') }}" class="nav-item active" title="Performance History">
            <!-- example chart icon representing performance -->
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18h18"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17l4-4 4 4 4-8"/>
            </svg>
        </a>
        <a href="{{ route('notifications.index') }}" class="nav-item" title="Notifications" style="position:relative;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            @if(auth()->check() && auth()->user()->unreadNotifications->count() > 0)
                <span style="position:absolute; top:6px; right:6px; width:10px; height:10px; background:#ef4444; border-radius:50%; border:2px solid white;"></span>
            @endif
        </a>
        <div class="nav-item" title="Settings">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
        </div>
        <div style="margin-top: auto;">
            <a href="#" id="logout-link" class="nav-item" title="Logout">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </aside>

    <!-- Notification Dropdown (fixed, appended to body by JS) -->
    <div id="notif-dropdown" style="display:none; position:fixed; width:300px; background:white; border-radius:12px; box-shadow:0 8px 30px rgba(0,0,0,0.15); border:1px solid #e5e7eb; overflow:hidden; z-index:99999; text-align:left;">
        <div style="padding:12px 14px; border-bottom:1px solid #e5e7eb; background:#f9fafb; display:flex; justify-content:space-between; align-items:center;">
            <span style="font-size:13px; font-weight:600; color:#374151;">Announcements</span>
            @if(auth()->check() && auth()->user()->unreadNotifications->count() > 0)
                <form action="{{ route('notifications.markAllAsRead') }}" method="POST" style="margin:0;">
                    @csrf
                    <button type="submit" style="font-size:11px; color:#2563eb; background:none; border:none; cursor:pointer;">Mark read</button>
                </form>
            @endif
        </div>
        <div style="max-height:320px; overflow-y:auto;">
            @if(auth()->check() && auth()->user()->notifications->count() > 0)
                @foreach(auth()->user()->unreadNotifications as $notification)
                    <div style="padding:12px 14px; border-bottom:1px solid #f3f4f6; background:#eff6ff;">
                        <p style="font-size:13px; font-weight:600; color:#1f2937; margin:0 0 4px;">{{ $notification->data['subject'] ?? 'New Announcement' }}</p>
                        <p style="font-size:11px; color:#6b7280; margin:0;">{{ Str::limit($notification->data['content'] ?? '', 80) }}</p>
                    </div>
                @endforeach
                @foreach(auth()->user()->readNotifications->take(5) as $notification)
                    <div style="padding:12px 14px; border-bottom:1px solid #f3f4f6; opacity:0.6;">
                        <p style="font-size:13px; font-weight:600; color:#1f2937; margin:0 0 4px;">{{ $notification->data['subject'] ?? 'Announcement' }}</p>
                        <p style="font-size:11px; color:#6b7280; margin:0;">{{ Str::limit($notification->data['content'] ?? '', 80) }}</p>
                    </div>
                @endforeach
            @else
                <div style="padding:24px; text-align:center; font-size:12px; color:#9ca3af;">No announcements yet</div>
            @endif
        </div>
    </div>

    <!-- logout confirmation modal -->
    <div class="modal-overlay" id="logout-modal">
        <div class="modal">
            <p>Are you sure you want to log out?</p>
            <button class="btn-cancel" id="cancel-logout">Cancel</button>
            <button class="btn-confirm" id="confirm-logout">
                Logout <span class="spinner" id="logout-spinner"></span>
            </button>
        </div>
    </div>

    <script>
        document.getElementById('logout-link').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('logout-modal').style.display = 'flex';
        });
        document.getElementById('cancel-logout').addEventListener('click', function() {
            document.getElementById('logout-modal').style.display = 'none';
        });
        document.getElementById('confirm-logout').addEventListener('click', function() {
            document.getElementById('logout-spinner').style.visibility = 'visible';
            document.getElementById('logout-form').submit();
        });

        function toggleNotifDropdown(e) {
            e.stopPropagation();
            var dropdown = document.getElementById('notif-dropdown');
            var btn = document.getElementById('notif-btn');
            if (dropdown.style.display === 'none' || dropdown.style.display === '') {
                // Move dropdown to body level so it overflows correctly
                document.body.appendChild(dropdown);
                var rect = btn.getBoundingClientRect();
                dropdown.style.position = 'fixed';
                dropdown.style.top = rect.top + 'px';
                dropdown.style.left = (rect.right + 12) + 'px';
                dropdown.style.display = 'block';
            } else {
                dropdown.style.display = 'none';
            }
        }
        document.addEventListener('click', function(e) {
            var btn = document.getElementById('notif-btn');
            var dropdown = document.getElementById('notif-dropdown');
            if (btn && dropdown && !btn.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });
    </script>

    <!-- Main Content -->
    <main class="main-content">
        <h1 class="page-title">Performance History</h1>

        <div class="card-container">
            <div class="card-header"></div>
            
            <div class="card-body">
                <!-- Filter Section -->
                <form action="{{ route('performance.index') }}" method="GET" class="filter-section">
                    <div class="filter-group">
                        <label class="filter-label">Semester</label>
                        <select name="semester" class="filter-select" onchange="this.form.submit()">
                            <option value="all" {{ $selectedSemester == 'all' ? 'selected' : '' }}>All Semesters</option>
                            <option value="first" {{ $selectedSemester == 'first' ? 'selected' : '' }}>First</option>
                            <option value="second" {{ $selectedSemester == 'second' ? 'selected' : '' }}>Second</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label class="filter-label">Year</label>
                        <select name="year" class="filter-select" onchange="this.form.submit()">
                            @for($y = 2024; $y <= 2030; $y++)
                                <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <a href="{{ route('performance.download', ['semester' => $selectedSemester, 'year' => $selectedYear]) }}" class="download-report-btn">
                        <svg class="download-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Download Report
                    </a>
                </form>

                <!-- Data Table -->
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Semester</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($performances as $performance)
                                <tr>
                                    <td>{{ $performance->date }}</td>
                                    <td>{{ $performance->semester }}</td>
                                    <td>
                                        <a href="{{ route('performance.show', $performance->id) }}" class="view-link">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="empty-state">No performance records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>
</html>