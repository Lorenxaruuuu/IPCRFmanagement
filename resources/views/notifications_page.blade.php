<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <title>Announcements - DSWD</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #e8f4f8 0%, #f0e6f6 50%, #ffe6e6 100%);
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
            text-decoration: none;
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

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 40px;
            overflow-y: auto;
        }

        .header {
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 28px;
            color: #2d3748;
            font-weight: 600;
        }

        .header .date {
            font-size: 14px;
            color: #718096;
        }

        .mark-all-btn {
            padding: 10px 20px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .mark-all-btn:hover {
            background: #5a6fd6;
        }

        /* Notifications Container */
        .notifications-container {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .notification-card {
            background: white;
            border-radius: 14px;
            padding: 20px 24px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            border-left: 4px solid transparent;
            display: flex;
            align-items: flex-start;
            gap: 16px;
            transition: box-shadow 0.2s;
        }

        .notification-card:hover {
            box-shadow: 0 4px 20px rgba(0,0,0,0.12);
        }

        .notification-card.unread {
            border-left-color: #667eea;
            background: #f7f8ff;
        }

        .notification-card.read {
            opacity: 0.7;
        }

        .notification-icon {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .notification-icon svg {
            width: 20px;
            height: 20px;
            color: white;
            stroke: white;
        }

        .notification-body {
            flex: 1;
        }

        .notification-subject {
            font-size: 15px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 6px;
        }

        .notification-content {
            font-size: 13px;
            color: #6b7280;
            line-height: 1.55;
        }

        .notification-time {
            font-size: 11px;
            color: #9ca3af;
            margin-top: 8px;
        }

        .priority-badge {
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 12px;
            flex-shrink: 0;
            align-self: flex-start;
            margin-top: 2px;
        }

        .priority-high {
            background: #fee2e2;
            color: #dc2626;
        }

        .priority-medium {
            background: #fef3c7;
            color: #d97706;
        }

        .priority-low {
            background: #d1fae5;
            color: #059669;
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: #9ca3af;
        }

        .empty-state svg {
            width: 64px;
            height: 64px;
            margin: 0 auto 16px;
            display: block;
            opacity: 0.4;
        }

        .empty-state h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #6b7280;
        }

        .empty-state p {
            font-size: 14px;
        }

        .logout-modal {
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

        .logout-modal.active {
            display: flex;
        }

        .modal {
            background: white;
            padding: 30px;
            border-radius: 12px;
            max-width: 320px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }

        .modal p {
            font-size: 14px;
            color: #4a5568;
            margin-bottom: 20px;
        }

        .modal button {
            margin: 0 5px;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
        }

        .btn-confirm {
            background: #1a73e8;
            color: white;
        }

        .btn-cancel {
            background: #e2e8f0;
            color: #4a5568;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 60px;
            }
            .main-content {
                padding: 20px;
            }
            .header {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <a href="{{ url('/home') }}" class="nav-item" title="Home">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
        </a>
        <a href="{{ url('/forms') }}" class="nav-item" title="Forms">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
            </svg>
        </a>
        <a href="{{ url('/performance') }}" class="nav-item" title="Performance History">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18h18"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17l4-4 4 4 4-8"/>
            </svg>
        </a>
        <a href="{{ route('notifications.index') }}" class="nav-item active" title="Announcements">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
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

    <!-- Logout Modal -->
    <div class="logout-modal" id="logout-modal">
        <div class="modal">
            <p>Are you sure you want to log out?</p>
            <button class="btn-cancel" id="cancel-logout">Cancel</button>
            <button class="btn-confirm" id="confirm-logout">Logout</button>
        </div>
    </div>

    <!-- Main Content -->
    <main class="main-content">
        <div class="header">
            <div>
                <h1>🔔 Announcements</h1>
                <div class="date">{{ now()->format('F j, Y') }}</div>
            </div>
        </div>

        <div class="notifications-container">
            @php
                $notices = DB::table('notices')
                    ->where('is_active', true)
                    ->orderBy('posted_at', 'desc')
                    ->get();
            @endphp

            @forelse($notices as $notice)
                @php
                    $priorityClass = match(strtolower($notice->priority)) {
                        'high' => 'priority-high',
                        'medium' => 'priority-medium',
                        default => 'priority-low',
                    };
                    $createdTime = \Carbon\Carbon::parse($notice->posted_at)->diffForHumans();
                @endphp
                <div class="notification-card unread">
                    <div class="notification-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                        </svg>
                    </div>
                    <div class="notification-body">
                        <div class="notification-subject">{{ $notice->subject }}</div>
                        <div class="notification-content">{{ $notice->content }}</div>
                        <div class="notification-time">{{ $createdTime }}</div>
                    </div>
                    <span class="priority-badge {{ $priorityClass }}">{{ $notice->priority }}</span>
                </div>
            @empty
                <div class="empty-state">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <h3>No announcements yet</h3>
                    <p>You'll see important announcements here when an admin posts them.</p>
                </div>
            @endforelse
        </div>
    </main>

    <script>
        // Logout functionality
        document.getElementById('logout-link').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('logout-modal').classList.add('active');
        });

        document.getElementById('cancel-logout').addEventListener('click', function() {
            document.getElementById('logout-modal').classList.remove('active');
        });

        document.getElementById('confirm-logout').addEventListener('click', function() {
            alert('Logging out...');
            // In production, this would submit the logout form
            // document.getElementById('logout-form').submit();
        });

        // Close modal when clicking outside
        document.getElementById('logout-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });
    </script>
</body>
</html>
