@extends('admin.layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
@yield('content')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IPCRF Admin Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        
        .glass-panel {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .sidebar-gradient {
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
        }
        
        .nav-item {
            transition: all 0.3s ease;
        }
        
        .nav-item:hover, .nav-item.active {
            background: rgba(255, 255, 255, 0.1);
            border-left: 4px solid #3b82f6;
        }
        
        .gradient-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .card-hover {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .priority-high { background: #fee2e2; color: #dc2626; }
        .priority-medium { background: #fef3c7; color: #d97706; }
        .priority-low { background: #dbeafe; color: #2563eb; }
        
        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .step-indicator {
            position: relative;
        }
        
        .step-indicator::after {
            content: '';
            position: absolute;
            top: 50%;
            right: -50%;
            width: 100%;
            height: 2px;
            background: #e5e7eb;
            z-index: 0;
        }
        
        .step-indicator:last-child::after {
            display: none;
        }
        
        .step-active {
            background: #3b82f6;
            color: white;
        }
        
        .step-completed {
            background: #10b981;
            color: white;
        }
    </style>
<div class="bg-gray-50" x-data="{ showLogoutModal: false, loggingOut: false }">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="sidebar-gradient w-64 flex-shrink-0 text-white flex flex-col">
            <div class="p-6 border-b border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line text-xl"></i>
                    </div>
                    <div>
                        <h1 class="font-bold text-lg">IPCRF Admin</h1>
                        <p class="text-xs text-gray-400">Management System</p>
                    </div>
                </div>
            </div>
            
            <nav class="flex-1 py-6">
                <a href="#" onclick="showView('dashboard')" class="nav-item active flex items-center gap-3 px-6 py-3 text-sm" id="nav-dashboard">
                    <i class="fas fa-home w-5"></i>
                    Dashboard Home
                </a>
                <a href="#" onclick="showView('upload')" class="nav-item flex items-center gap-3 px-6 py-3 text-sm" id="nav-upload">
                    <i class="fas fa-upload w-5"></i>
                    Update/Upload IPCRF
                </a>
                <a href="#" onclick="showView('records')" class="nav-item flex items-center gap-3 px-6 py-3 text-sm" id="nav-records">
                    <i class="fas fa-list w-5"></i>
                    List of Uploaded
                </a>
                <a href="#" onclick="showView('notices')" class="nav-item flex items-center gap-3 px-6 py-3 text-sm" id="nav-notices">
                    <i class="fas fa-bell w-5"></i>
                    Manage Notices
                </a>
                <a href="#" onclick="showView('forms')" class="nav-item flex items-center gap-3 px-6 py-3 text-sm" id="nav-forms">
                    <i class="fas fa-file-alt w-5"></i>
                    Manage Forms
                </a>
            </nav>
            
            <div class="p-4 border-t border-gray-700">
                <div class="flex items-center gap-3 px-2">
                    <img src="https://ui-avatars.com/api/?name=Admin+User&background=3b82f6&color=fff" class="w-10 h-10 rounded-full">
                    <div class="flex-1">
                        <p class="text-sm font-medium">Administrator</p>
                        <p class="text-xs text-gray-400">admin@deped.gov.ph</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-auto">
            <!-- Top Header -->
            <header class="glass-panel sticky top-0 z-40 px-8 py-4 flex justify-between items-center border-b">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800" id="page-title">Admin Dashboard Overview</h2>
                    <p class="text-sm text-gray-500">Manage IPCRF records and system announcements</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="relative">
                        @php
                            $allNotifications = collect();
                            foreach($announcements as $notice) {
                                $allNotifications->push((object)[
                                    'type' => 'notice',
                                    'title' => $notice->subject,
                                    'content' => $notice->content,
                                    'priority' => $notice->priority,
                                    'date' => $notice->posted_at,
                                    'link' => "showView('notices'); toggleNotifications();"
                                ]);
                            }
                            if(isset($forms)) {
                                foreach($forms as $form) {
                                    $allNotifications->push((object)[
                                        'type' => 'form',
                                        'title' => 'New ' . $form->category . ' Form',
                                        'content' => $form->title . ' - ' . substr($form->description, 0, 50),
                                        'priority' => 'Info',
                                        'date' => $form->published_at ?? now(),
                                        'link' => "showView('forms'); toggleNotifications();"
                                    ]);
                                }
                            }
                            $allNotifications = $allNotifications->sortByDesc('date')->take(10);
                        @endphp

                        <button onclick="toggleNotifications()" id="notification-btn" class="relative p-2 text-gray-600 hover:text-gray-800 transition-colors">
                            <i class="fas fa-bell text-xl"></i>
                            @if($allNotifications->count() > 0)
                                <span id="notification-badge" class="absolute top-0 right-0 w-3 h-3 bg-red-500 rounded-full"></span>
                            @endif
                        </button>

                        <div id="notification-dropdown" class="hidden absolute top-full right-0 mt-2 w-80 glass-panel rounded-xl shadow-lg border border-gray-200 z-50 overflow-hidden fade-in text-left">
                            <div class="p-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                                <h3 class="font-bold text-gray-800">Notifications</h3>
                                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full font-semibold">{{ $allNotifications->count() }} New</span>
                            </div>
                            <div class="max-h-96 overflow-y-auto">
                                @forelse($allNotifications as $notification)
                                <div class="p-4 border-b border-gray-100 hover:bg-gray-50 transition cursor-pointer" onclick="{!! $notification->link !!}">
                                    <div class="flex justify-between items-start mb-1">
                                        <h4 class="font-semibold text-sm text-gray-800">{{ $notification->title }}</h4>
                                        <span class="status-badge {{ $notification->type === 'form' ? 'bg-blue-100 text-blue-700' : 'priority-' . strtolower($notification->priority) }} text-[10px]">{{ $notification->priority }}</span>
                                    </div>
                                    <p class="text-xs text-gray-600 line-clamp-2 mb-2 whitespace-pre-wrap">{{ $notification->content }}</p>
                                    <p class="text-[10px] text-gray-400">{{ $notification->date->diffForHumans() }}</p>
                                </div>
                                @empty
                                <div class="p-4 text-center text-sm text-gray-500">No new notifications</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <button @click="showLogoutModal = true" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                        <i class="fas fa-sign-out-alt"></i>Logout
                    </button>
                </div>
            </header>

            <div class="p-8">
                <!-- DASHBOARD VIEW -->
                <div id="view-dashboard" class="view-section fade-in">
                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="glass-panel rounded-2xl p-6 card-hover border-l-4 border-blue-500 cursor-pointer" onclick="showView('records')">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-gray-500 text-sm mb-1">IPCRF Uploaded</p>
                                    <h3 class="text-3xl font-bold text-gray-800" id="uploaded-count">{{ $stats['uploaded_employees'] }}</h3>
                                    <p class="text-xs text-gray-500">({{ $stats['total_uploaded'] }} file{{ $stats['total_uploaded'] == 1 ? '' : 's' }} uploaded)</p>
                                </div>
                                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-file-alt text-blue-600 text-xl"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="glass-panel rounded-2xl p-6 card-hover border-l-4 border-green-500 cursor-pointer" onclick="showView('forms')">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-gray-500 text-sm mb-1">Active Forms</p>
                                    <h3 class="text-3xl font-bold text-gray-800">{{ $stats['active_forms'] }}</h3>
                                </div>
                                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="glass-panel rounded-2xl p-6 card-hover border-l-4 border-orange-500 cursor-pointer" onclick="showView('notices')">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-gray-500 text-sm mb-1">Notices</p>
                                    <h3 class="text-3xl font-bold text-gray-800" id="notices-count">{{ $stats['notices'] }}</h3>
                                </div>
                                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-bell text-orange-600 text-xl"></i>
                                </div>
                            </div>
                        </div>
                        <div class="glass-panel rounded-2xl p-6 card-hover border-l-4 border-teal-500">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-gray-500 text-sm mb-1">Total Employees</p>
                                    <h3 class="text-3xl font-bold text-gray-800" id="staff-count">{{ $stats['total_employees'] }}</h3>
                                </div>
                                <div class="w-12 h-12 bg-teal-100 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-users text-teal-600 text-xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Recent Submissions -->
                        <div class="lg:col-span-2 glass-panel rounded-2xl p-6">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-lg font-bold text-gray-800">Recent IPCRF Submissions</h3>
                                <button onclick="showView('records')" class="text-blue-600 text-sm hover:underline">View All</button>
                            </div>
                            <p class="text-xs text-gray-500 mb-4">Latest records from regional encoders</p>
                            
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="text-left text-xs text-gray-500 border-b">
                                            <th class="pb-3 font-medium">Employee</th>
                                            <th class="pb-3 font-medium">Region</th>
                                            <th class="pb-3 font-medium">Date Uploaded</th>
                                            <th class="pb-3 font-medium">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="dashboard-recent-submissions" class="text-sm">
                                        <tr><td colspan="4" class="text-center py-4 text-gray-500">Loading...</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Latest Announcements -->
                        <div class="glass-panel rounded-2xl p-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-1">Latest Announcements</h3>
                            <p class="text-xs text-gray-500 mb-4">Broadcasted to all users</p>
                            
                            <div class="space-y-4">
                                @foreach($announcements as $notice)
                                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                        <div class="flex justify-between items-start mb-2">
                                            <h4 class="font-semibold text-sm">{{ $notice->subject }}</h4>
                                            <span class="status-badge priority-{{ strtolower($notice->priority) }}">{{ $notice->priority }}</span>
                                        </div>
                                        <p class="text-xs text-gray-600 mb-2">{{ $notice->content }}</p>
                                        <p class="text-xs text-gray-400">Posted on {{ $notice->posted_at->format('M j, Y') }}</p>
                                    </div>
                                @endforeach
                            </div>
                            
                            <button onclick="showView('notices')" class="w-full mt-4 text-blue-600 text-sm font-medium hover:underline">
                                View All Notices
                            </button>
                        </div>
                    </div>
                </div>

                <!-- UPLOAD/UPDATE IPCRF VIEW -->
                <div id="view-upload" class="view-section hidden fade-in">
                    <div class="max-w-4xl mx-auto">
                        <!-- Progress Steps -->
                        <div class="flex justify-between mb-8 px-4">
                            <div class="step-indicator flex-1 text-center" id="step-1">
                                <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center mx-auto mb-2 font-semibold">1</div>
                                <p class="text-sm font-medium">Select Role</p>
                            </div>
                            <div class="step-indicator flex-1 text-center" id="step-2">
                                <div class="w-10 h-10 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center mx-auto mb-2 font-semibold">2</div>
                                <p class="text-sm font-medium">Upload Form</p>
                            </div>
                            <div class="step-indicator flex-1 text-center" id="step-3">
                                <div class="w-10 h-10 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center mx-auto mb-2 font-semibold">3</div>
                                <p class="text-sm font-medium">Confirmation</p>
                            </div>
                        </div>

                        <!-- Step 1: Select Role -->
                        <div id="upload-step-1" class="glass-panel rounded-2xl p-8">
                            <h3 class="text-xl font-bold mb-6">Select Role to Update IPCRF</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <button onclick="selectRole('Teacher')" class="p-6 border-2 border-gray-200 rounded-xl hover:border-blue-500 hover:bg-blue-50 transition text-left group">
                                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4 group-hover:bg-blue-200">
                                        <i class="fas fa-chalkboard-teacher text-blue-600 text-xl"></i>
                                    </div>
                                    <h4 class="font-bold text-lg mb-1">Teacher</h4>
                                    <p class="text-sm text-gray-500">Update IPCRF for teaching staff</p>
                                </button>
                                
                                <button onclick="selectRole('Master Teacher')" class="p-6 border-2 border-gray-200 rounded-xl hover:border-blue-500 hover:bg-blue-50 transition text-left group">
                                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4 group-hover:bg-purple-200">
                                        <i class="fas fa-user-tie text-purple-600 text-xl"></i>
                                    </div>
                                    <h4 class="font-bold text-lg mb-1">Master Teacher</h4>
                                    <p class="text-sm text-gray-500">Update IPCRF for master teachers</p>
                                </button>
                                
                                <button onclick="selectRole('Principal')" class="p-6 border-2 border-gray-200 rounded-xl hover:border-blue-500 hover:bg-blue-50 transition text-left group">
                                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4 group-hover:bg-green-200">
                                        <i class="fas fa-school text-green-600 text-xl"></i>
                                    </div>
                                    <h4 class="font-bold text-lg mb-1">Principal</h4>
                                    <p class="text-sm text-gray-500">Update IPCRF for school heads</p>
                                </button>
                                
                                <button onclick="selectRole('Supervisor')" class="p-6 border-2 border-gray-200 rounded-xl hover:border-blue-500 hover:bg-blue-50 transition text-left group">
                                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mb-4 group-hover:bg-orange-200">
                                        <i class="fas fa-users-cog text-orange-600 text-xl"></i>
                                    </div>
                                    <h4 class="font-bold text-lg mb-1">Supervisor</h4>
                                    <p class="text-sm text-gray-500">Update IPCRF for supervisors</p>
                                </button>
                            </div>
                        </div>

                        <!-- Step 2: Upload Form -->
                        <div id="upload-step-2" class="glass-panel rounded-2xl p-8 hidden">
                            @if($errors->any())
                                <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                                    {{ implode(' ', $errors->all()) }}
                                </div>
                            @endif
                            @if(session('success'))
                                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
                                    {{ session('success') }}
                                </div>
                            @endif
                            @if(session('warning'))
                                <div class="mb-4 p-3 bg-yellow-100 text-yellow-700 rounded">
                                    {{ session('warning') }}
                                </div>
                            @endif
                            <div class="flex items-center gap-2 mb-6">
                                <button onclick="prevStep()" class="text-gray-500 hover:text-gray-700">
                                    <i class="fas fa-arrow-left"></i>
                                </button>
                                <h3 class="text-xl font-bold">Upload IPCRF Form - <span id="selected-role" class="text-blue-600"></span></h3>
                            </div>
                            
                            <form id="uploadForm" class="space-y-6" action="/api/upload.php" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="role" id="hidden-role" value="{{ old('role') }}">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Employee Name</label>
                                        <input type="text" name="employee_name" value="{{ old('employee_name') }}" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Search employee...">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Employee ID</label>
                                        <input type="text" name="employee_id" value="{{ old('employee_id') }}" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="e.g., 2024-00123">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Province</label>
                                        <select id="province-select" name="province_id" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500">
                                            <option value="">Select Province</option>
                                            @foreach($provinces as $province)
                                                <option value="{{ $province->id }}" {{ old('province_id') == $province->id ? 'selected' : '' }}>{{ $province->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Municipality</label>
                                        <select id="municipality-select" name="municipality_id" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500">
                                            <option value="">Select Municipality</option>
                                            @if(old('municipality_id') && old('province_id'))
                                                @php
                                                    $oldMuns = \App\Models\Municipality::where('province_id', old('province_id'))->get();
                                                @endphp
                                                @foreach($oldMuns as $mun)
                                                    <option value="{{ $mun->id }}" {{ old('municipality_id') == $mun->id ? 'selected' : '' }}>{{ $mun->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">School</label>
                                        <select id="school_id" name="school_id" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500">
                                            <option value="">Select School</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Semester</label>
                                        <select name="semester" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500">
                                            <option value="1st" {{ old('semester') == '1st' ? 'selected' : '' }}>1st Semester</option>
                                            <option value="2nd" {{ old('semester') == '2nd' ? 'selected' : '' }}>2nd Semester</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">School Year</label>
                                        <select name="school_year" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500">
                                            <option {{ old('school_year') == '2027-2028' ? 'selected' : '' }}>2027-2028</option>
                                            <option {{ old('school_year') == '2026-2027' ? 'selected' : '' }}>2026-2027</option>
                                            <option {{ old('school_year') == '2025-2026' ? 'selected' : '' }}>2025-2026</option>
                                            <option {{ old('school_year') == '2024-2025' ? 'selected' : '' }}>2024-2025</option>
                                            <option {{ old('school_year') == '2023-2024' ? 'selected' : '' }}>2023-2024</option>
                                            <option {{ old('school_year') == '2022-2023' ? 'selected' : '' }}>2022-2023</option>
                                        </select>
                                    </div>
                                </div>

                                <div id="file-dropzone" class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-blue-500 transition cursor-pointer bg-gray-50">
                                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                                    <p class="text-gray-600 mb-1">Click to upload or drag and drop</p>
                                    <p class="text-sm text-gray-400">PDF, Excel, Word files up to 10MB</p>
                                    <input type="file" name="file" class="hidden" accept=".pdf,.xlsx,.xls,.doc,.docx" required>
                                    <p id="file-name" class="text-sm text-gray-600 mt-2"></p>
                                </div>

                                <div class="flex gap-4">
                                    <button type="button" onclick="prevStep()" class="flex-1 px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Back</button>
                                    <button type="button" onclick="nextStep()" class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Continue</button>
                                </div>
                            </form>
                        </div>

                        <!-- Step 3: Confirmation -->
                        <div id="upload-step-3" class="glass-panel rounded-2xl p-8 hidden text-center">
                            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-check text-green-600 text-3xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold mb-2">IPCRF Successfully Updated!</h3>
                            <p class="text-gray-600 mb-6">The IPCRF form has been uploaded and saved to the system.</p>
                            
                            <div class="bg-gray-50 rounded-lg p-4 max-w-md mx-auto mb-6 text-left">
                                <div class="flex justify-between py-2 border-b border-gray-200">
                                    <span class="text-gray-600">Employee Name:</span>
                                    <span class="font-medium">Loading...</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-gray-200">
                                    <span class="text-gray-600">Employee ID:</span>
                                    <span class="font-medium">—</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-gray-200">
                                    <span class="text-gray-600">Role:</span>
                                    <span class="font-medium" id="confirm-role">Teacher</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-gray-200">
                                    <span class="text-gray-600">Province:</span>
                                    <span class="font-medium">—</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-gray-200">
                                    <span class="text-gray-600">Municipality:</span>
                                    <span class="font-medium">—</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-gray-200">
                                    <span class="text-gray-600">School:</span>
                                    <span class="font-medium">—</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-gray-200">
                                    <span class="text-gray-600">Semester:</span>
                                    <span class="font-medium">—</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-gray-200">
                                    <span class="text-gray-600">School Year:</span>
                                    <span class="font-medium">—</span>
                                </div>
                                <div class="flex justify-between py-2">
                                    <span class="text-gray-600">Status:</span>
                                    <span class="status-badge bg-green-100 text-green-700">Successfully Uploaded</span>
                                </div>
                            </div>
                            
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 text-sm text-blue-700">
                                <i class="fas fa-info-circle mr-2"></i>
                                Redirecting to dashboard in <span id="countdown">3</span> seconds...
                            </div>
                            
                            <div class="flex gap-4 justify-center">
                                <button onclick="resetUpload()" class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Upload Another</button>
                                <button onclick="showView('records')" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">View Records</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RECORDS DATABASE VIEW -->
                <div id="view-records" class="view-section hidden fade-in">
                    <div class="glass-panel rounded-2xl p-6">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                            <div>
                                <h3 class="text-xl font-bold text-gray-800">IPCRF Records Database</h3>
                                <p class="text-sm text-gray-500">Manage and download uploaded IPCRF forms</p>
                            </div>
                            <button onclick="downloadReport()" class="bg-blue-900 text-white px-6 py-2 rounded-lg hover:bg-blue-800 transition flex items-center gap-2">
                                <i class="fas fa-download"></i>
                                Download Report
                            </button>
                        </div>

                        <!-- Filters -->
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6 hidden" id="records-filters">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase">Employee ID</label>
                                <input type="text" id="filter-employee-id" onchange="filterRecords()" placeholder="Search by Employee ID" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase">Province</label>
                                <select id="filter-province" onchange="loadDashboardMunicipalities(this.value); filterRecords();" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                                    <option value="">All Provinces</option>
                                    @foreach($provinces as $province)
                                        <option value="{{ $province->id }}">{{ $province->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase">Municipality</label>
                                <select id="filter-municipality" onchange="filterRecords()" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                                    <option value="">All Municipalities</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase">Semester</label>
                                <select id="filter-semester" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                                    <option value="">All Semesters</option>
                                    <option value="1st Semester">1st Semester</option>
                                    <option value="2nd Semester">2nd Semester</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase">Year</label>
                                <select id="filter-year" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                                    <option value="">All Years</option>
                                    <option>2027</option>
                                    <option>2026</option>
                                    <option>2025</option>
                                    <option>2024</option>
                                    <option>2023</option>
                                    <option>2022</option>
                                    <option>2021</option>
                                </select>
                            </div>
                        </div>

                        <!-- Records Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="text-left border-b-2 border-gray-200">
                                        <th class="pb-3 font-semibold text-sm text-gray-700">Employee</th>
                                        <th class="pb-3 font-semibold text-sm text-gray-700">Region</th>
                                        <th class="pb-3 font-semibold text-sm text-gray-700">Date Uploaded</th>
                                        <th class="pb-3 font-semibold text-sm text-gray-700">Status</th>
                                        <th class="pb-3 font-semibold text-sm text-gray-700">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="records-table-body" class="text-sm">
                                    @foreach($recentSubmissions as $record)
                                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                                            <td class="py-4 font-medium">{{ $record->employee?->fullName() ?? 'N/A' }}</td>
                                            <td class="py-4 text-gray-600">{{ optional(optional(optional($record->employee)->school)->municipality)->province->name ?? '' }}</td>
                                            <td class="py-4 text-gray-600">{{ $record->uploaded_at ? $record->uploaded_at->format('F j, Y') : 'N/A' }}</td>
                                            <td class="py-4"><span class="status-badge bg-green-100 text-green-700">{{ $record->status }}</span></td>
                                            <td class="py-4">
                                                @if($record->id)
                                                    <a href="{{ route('admin.records.download', $record->id) }}" class="text-gray-400 hover:text-blue-600 transition">
                                                        <i class="fas fa-download text-lg"></i>
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

                        
                    </div>
                </div>

                <!-- NOTICES/ANNOUNCEMENTS VIEW -->
                <div id="view-notices" class="view-section hidden fade-in">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Create Notice Form -->
                        <div class="glass-panel rounded-2xl p-6">
                            <h3 class="text-xl font-bold mb-1">Create New Notice</h3>
                            <p class="text-sm text-gray-500 mb-6">Post announcements for all users</p>
                            
                            <form id="noticeForm" method="POST" action="{{ route('admin.notices.store') }}" onsubmit="postNotice(event)" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase">Subject</label>
                                    <input type="text" id="notice-subject" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500" placeholder="e.g., Deadline Extension" required>
                                </div>
                                
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-2 uppercase">Priority Level</label>
                                    <div class="flex gap-3">
                                        <label class="flex-1 cursor-pointer">
                                            <input type="radio" name="priority" value="Low" class="hidden peer">
                                            <div class="text-center py-2 border-2 rounded-lg peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600 hover:bg-gray-50 transition text-sm font-medium">
                                                LOW
                                            </div>
                                        </label>
                                        <label class="flex-1 cursor-pointer">
                                            <input type="radio" name="priority" value="Medium" class="hidden peer">
                                            <div class="text-center py-2 border-2 rounded-lg peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600 hover:bg-gray-50 transition text-sm font-medium">
                                                MEDIUM
                                            </div>
                                        </label>
                                        <label class="flex-1 cursor-pointer">
                                            <input type="radio" name="priority" value="High" class="hidden peer" checked>
                                            <div class="text-center py-2 border-2 rounded-lg peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600 hover:bg-gray-50 transition text-sm font-medium">
                                                HIGH
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase">Content</label>
                                    <textarea id="notice-content" rows="5" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 resize-none" placeholder="Write your message here..." required></textarea>
                                </div>
                                
                                <button type="submit" class="w-full bg-blue-900 text-white py-3 rounded-lg hover:bg-blue-800 transition font-medium">
                                    Post Announcement
                                </button>
                            </form>
                        </div>

                        <!-- Active Announcements -->
                        <div class="glass-panel rounded-2xl p-6">
                            <h3 class="text-xl font-bold mb-6">Active Announcements</h3>
                            
                            <div id="announcements-list" class="space-y-4">
                                @foreach($announcements as $notice)
                                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 relative group" data-id="{{ $notice->id }}">
                                        <div class="flex justify-between items-start mb-2">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-info text-blue-600"></i>
                                                </div>
                                                <div>
                                                    <h4 class="font-bold text-gray-800">{{ $notice->subject }}</h4>
                                                    <p class="text-xs text-gray-500">Posted on {{ $notice->posted_at->format('d/m/Y') }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="status-badge priority-{{ strtolower($notice->priority) }}">{{ $notice->priority }}</span>
                                                <button onclick="deleteNotice({{ $notice->id }}, this)" class="text-gray-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <p class="text-sm text-gray-600 ml-13 pl-13">{{ $notice->content }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MANAGE FORMS VIEW -->
                <div id="view-forms" class="view-section hidden fade-in">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Upload New Form -->
                        <div class="glass-panel rounded-2xl p-6">
                            <h3 class="text-xl font-bold mb-1">Upload New Form</h3>
                            <p class="text-sm text-gray-500 mb-6">Add documents for encoders to download</p>
                            
                            <form method="POST" action="{{ route('admin.forms.store') }}" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase">Form Title</label>
                                    <input type="text" name="title" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500" placeholder="e.g., IPCRF Template 2025">
                                </div>
                                
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase">Category</label>
                                    <select name="category" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500">
                                        <option value="Template">Template</option>
                                        <option value="Guidelines">Guidelines</option>
                                        <option value="Reference">Reference</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase">Description</label>
                                    <textarea name="description" rows="3" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 resize-none" placeholder="Briefly describe the form..."></textarea>
                                </div>
                                
                                <label class="block cursor-pointer">
                                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-blue-500 transition bg-gray-50">
                                        <i class="fas fa-file-upload text-3xl text-gray-400 mb-2"></i>
                                        <p class="text-sm text-gray-600 file-name-display">Click to upload or drag and drop</p>
                                        <p class="text-xs text-gray-400">PDF, DOC, XLS files</p>
                                        <input type="file" name="file" class="hidden" accept=".pdf,.doc,.docx,.xls,.xlsx" required onchange="this.parentElement.querySelector('.file-name-display').textContent = this.files[0].name">
                                    </div>
                                </label>
                                
                                <button type="submit" class="w-full bg-blue-900 text-white py-3 rounded-lg hover:bg-blue-800 transition font-medium">
                                    Publish Form
                                </button>
                            </form>
                        </div>

                        <!-- Published Forms -->
                        <div class="glass-panel rounded-2xl p-6">
                            <h3 class="text-xl font-bold mb-6">Published Forms</h3>
                            
                            <div class="space-y-4">
                                @forelse($forms as $form)
                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                                <i class="fas {{ str_contains(strtolower($form->file_name ?? ''), 'pdf') ? 'fa-file-pdf' : (str_contains(strtolower($form->file_name ?? ''), 'xl') ? 'fa-file-excel' : 'fa-file-alt') }} text-gray-600 text-xl"></i>
                                            </div>
                                            <div>
                                                @php
                                                    $bgClass = 'bg-gray-200 text-gray-700';
                                                    if($form->category == 'Guidelines') $bgClass = 'bg-blue-100 text-blue-700';
                                                    elseif($form->category == 'Reference') $bgClass = 'bg-green-100 text-green-700';
                                                @endphp
                                                <span class="inline-block px-2 py-1 {{ $bgClass }} text-xs rounded mb-1">{{ $form->category }}</span>
                                                <h4 class="font-bold text-gray-800">{{ $form->title }}</h4>
                                                <p class="text-xs text-gray-500">{{ Str::limit($form->description, 60) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                                        <span class="text-xs text-gray-500">{{ $form->published_at ? $form->published_at->format('d/m/Y') : 'N/A' }}</span>
                                        <div class="flex gap-4">
                                            <form action="{{ route('admin.forms.destroy', $form->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this form?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 text-sm font-medium hover:underline flex items-center gap-1">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                            <a href="{{ route('admin.forms.download', $form->id) }}" class="text-blue-600 text-sm font-medium hover:underline flex items-center gap-1">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center py-6 text-gray-500 text-sm">
                                    No forms published yet.
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Report Filter Modal -->
    <div id="report-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="glass-panel rounded-2xl p-6 max-w-2xl w-full mx-4 transform scale-95 opacity-0 transition-all duration-300" id="report-modal-content">
            <div class="mb-6">
                <h3 class="text-xl font-bold mb-2">Generate IPCRF Records Report</h3>
                <p class="text-sm text-gray-600">Select filters to customize your report</p>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase">Province</label>
                    <select id="report-province" onchange="loadReportMunicipalities(this.value);" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                        <option value="">All Provinces</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province->id }}">{{ $province->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase">Municipality</label>
                    <select id="report-municipality" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                        <option value="">All Municipalities</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase">Semester</label>
                    <select id="report-semester" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                        <option value="">All Semesters</option>
                        <option value="1st Semester">1st Semester</option>
                        <option value="2nd Semester">2nd Semester</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1 uppercase">Year</label>
                    <select id="report-year" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                        <option value="">All Years</option>
                        <option>2027</option>
                        <option>2026</option>
                        <option>2025</option>
                        <option>2024</option>
                        <option>2023</option>
                        <option>2022</option>
                        <option>2021</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-3 justify-end">
                <button onclick="closeReportModal()" class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 transition font-medium">
                    Cancel
                </button>
                <button onclick="proceedDownloadReport()" class="px-4 py-2 bg-blue-900 text-white rounded-lg hover:bg-blue-800 transition font-medium flex items-center gap-2">
                    <i class="fas fa-download"></i>
                    Download Report
                </button>
            </div>
        </div>
    </div>
    <div id="alert-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="glass-panel rounded-2xl p-6 max-w-md w-full mx-4 transform scale-95 opacity-0 transition-all duration-300" id="alert-content">
            <div class="text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">System Alert</h3>
                <p class="text-gray-600 mb-6" id="alert-message">Please complete the required fields before proceeding.</p>
                <button onclick="closeAlert()" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition">
                    Acknowledge
                </button>
            </div>
        </div>
    </div>

    <script>
        let currentStep = 1;
        let selectedRole = '';
        let uploadFormData = {}; // Store form data for confirmation

        function showView(viewName) {
            // Hide all views
            document.querySelectorAll('.view-section').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.nav-item').forEach(el => el.classList.remove('active'));
            
            // Show selected view
            document.getElementById(`view-${viewName}`).classList.remove('hidden');
            document.getElementById(`nav-${viewName}`).classList.add('active');
            
            // Update page title
            const titles = {
                'dashboard': 'Admin Dashboard Overview',
                'upload': 'Update/Upload IPCRF',
                'records': 'IPCRF Records Database',
                'notices': 'Regional Announcements',
                'forms': 'Manage Downloadable Forms'
            };
            document.getElementById('page-title').textContent = titles[viewName];
            
            // Load records when records view is shown
            if (viewName === 'records') {
                loadDashboardRecords();
            }
        }

        function selectRole(role) {
            selectedRole = role;
            document.getElementById('selected-role').textContent = role;
            document.getElementById('confirm-role').textContent = role;
            document.getElementById('hidden-role').value = role;
            nextStep();
        }

        function nextStep() {
            if (currentStep === 2) {
                // Submit form via API
                submitUploadForm();
                return;
            }
            if (currentStep < 3) {
                document.getElementById(`upload-step-${currentStep}`).classList.add('hidden');
                currentStep++;
                document.getElementById(`upload-step-${currentStep}`).classList.remove('hidden');
                updateStepIndicator();
            }
        }

        function submitUploadForm() {
            const form = document.getElementById('uploadForm');
            const submitBtn = form.querySelector('button[type="button"]');
            const btnText = submitBtn.innerHTML;

            // guard against multiple calls
            if (submitBtn.disabled) {
                return;
            }

            // Disable submit button and show spinner
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';

            const formData = new FormData(form);
            
            // Store form data for confirmation display
            uploadFormData = {
                employee_name: form.querySelector('[name="employee_name"]').value,
                employee_id: form.querySelector('[name="employee_id"]').value,
                role: form.querySelector('[name="role"]').value,
                province_id: form.querySelector('[name="province_id"]').value,
                municipality_id: form.querySelector('[name="municipality_id"]').value,
                school_id: form.querySelector('[name="school_id"]').value,
                semester: form.querySelector('[name="semester"]').value,
                school_year: form.querySelector('[name="school_year"]').value,
                // Get display names
                province_name: document.querySelector('[name="province_id"] option:checked').textContent,
                municipality_name: document.querySelector('[name="municipality_id"] option:checked').textContent,
                school_name: document.querySelector('[name="school_id"] option:checked').textContent
            };

            fetch('/api/upload.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    // Check if there's a warning about Google Drive upload
                    if (response.warning) {
                        // Show warning but still show confirmation
                        showAlert('⚠️ Partial Upload', response.message || 'File saved but Google Drive upload failed', 'warning');
                    }
                    
                    // Update confirmation display with actual data
                    displayConfirmationData(response.data);
                    
                    // show confirmation step directly without re‑submitting
                    document.getElementById('upload-step-2').classList.add('hidden');
                    document.getElementById('upload-step-3').classList.remove('hidden');
                    currentStep = 3;
                    updateStepIndicator();
                    
                    // Auto-refresh page after 3 seconds
                    setTimeout(() => {
                        location.reload();
                    }, 3000);
                } else {
                    showAlert('Upload Failed', response.error || 'An error occurred', 'warning');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = btnText;
                }
            })
            .catch(err => {
                showAlert('Error', 'Failed to upload: ' + err.message, 'warning');
                submitBtn.disabled = false;
                submitBtn.innerHTML = btnText;
            });
        }
        
        function displayConfirmationData(apiData) {
            // Update confirmation fields with actual data
            const confirmContent = document.querySelector('#upload-step-3 .bg-gray-50');
            if (confirmContent) {
                confirmContent.innerHTML = `
                    <div class="flex justify-between py-2 border-b border-gray-200">
                        <span class="text-gray-600">Employee Name:</span>
                        <span class="font-medium">${escapeHtml(uploadFormData.employee_name)}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-200">
                        <span class="text-gray-600">Employee ID:</span>
                        <span class="font-medium">${escapeHtml(uploadFormData.employee_id)}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-200">
                        <span class="text-gray-600">Role:</span>
                        <span class="font-medium">${escapeHtml(uploadFormData.role)}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-200">
                        <span class="text-gray-600">Province:</span>
                        <span class="font-medium">${escapeHtml(uploadFormData.province_name)}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-200">
                        <span class="text-gray-600">Municipality:</span>
                        <span class="font-medium">${escapeHtml(uploadFormData.municipality_name)}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-200">
                        <span class="text-gray-600">School:</span>
                        <span class="font-medium">${escapeHtml(uploadFormData.school_name)}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-200">
                        <span class="text-gray-600">Semester:</span>
                        <span class="font-medium">${escapeHtml(uploadFormData.semester)}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-200">
                        <span class="text-gray-600">School Year:</span>
                        <span class="font-medium">${escapeHtml(uploadFormData.school_year)}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-gray-600">Status:</span>
                        <span class="status-badge bg-green-100 text-green-700">Successfully Uploaded</span>
                    </div>
                `;
            }
            
            // Start countdown timer
            startCountdown();
        }
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function prevStep() {
            if (currentStep > 1) {
                document.getElementById(`upload-step-${currentStep}`).classList.add('hidden');
                currentStep--;
                document.getElementById(`upload-step-${currentStep}`).classList.remove('hidden');
                updateStepIndicator();
            }
        }
        
        function startCountdown() {
            let count = 3;
            const countdownEl = document.getElementById('countdown');
            
            const countdownInterval = setInterval(() => {
                count--;
                if (countdownEl) {
                    countdownEl.textContent = count;
                }
                
                if (count <= 0) {
                    clearInterval(countdownInterval);
                }
            }, 1000);
        }

        function updateStepIndicator() {
            for (let i = 1; i <= 3; i++) {
                const stepEl = document.getElementById(`step-${i}`).querySelector('div');
                if (i < currentStep) {
                    stepEl.className = 'w-10 h-10 rounded-full bg-green-500 text-white flex items-center justify-center mx-auto mb-2 font-semibold';
                    stepEl.innerHTML = '<i class="fas fa-check"></i>';
                } else if (i === currentStep) {
                    stepEl.className = 'w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center mx-auto mb-2 font-semibold';
                    stepEl.textContent = i;
                } else {
                    stepEl.className = 'w-10 h-10 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center mx-auto mb-2 font-semibold';
                    stepEl.textContent = i;
                }
            }
        }

        function resetUpload() {
            currentStep = 1;
            document.getElementById('upload-step-3').classList.add('hidden');
            document.getElementById('upload-step-1').classList.remove('hidden');
            updateStepIndicator();
            document.getElementById('uploadForm').reset();
        }

        const noticeBaseUrl = `{{ url('admin/notices') }}`;

        function postNotice(e) {
            e.preventDefault();
            const subject = document.getElementById('notice-subject').value;
            const content = document.getElementById('notice-content').value;
            let priority = document.querySelector('input[name="priority"]:checked').value;
            // make sure case matches server enum (Low/Medium/High)
            priority = priority.charAt(0).toUpperCase() + priority.slice(1).toLowerCase();
            const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch(noticeBaseUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ subject, content, priority })
            })
            .then(res => {
                if (!res.ok) {
                    return res.json().then(err => { throw err; });
                }
                return res.json();
            })
            .then(() => {
                location.reload();
            })
            .catch(err => {
                console.error('Failed to post notice', err);
                alert('Unable to post announcement: ' + (err.message || JSON.stringify(err)));
            });
        }

        function deleteNotice(id, btn) {
            if (!confirm('Are you sure you want to delete this announcement?')) return;
            const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch(`${noticeBaseUrl}/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(() => location.reload());
        }

        function filterRecords() {
            // request fresh data from server
            loadDashboardRecords();
        }

        function loadDashboardRecords() {
            fetch('/admin/get_records.php')
                .then(res => res.json())
                .then(response => {
                    const tbody = document.getElementById('records-table-body');
                    tbody.innerHTML = '';
                    
                    if (!response.success || !response.data) {
                        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-gray-500">No records found</td></tr>';
                        return;
                    }
                    
                    response.data.forEach(r => {
                        const tr = document.createElement('tr');
                        tr.className = 'border-b border-gray-100 hover:bg-gray-50';

                        const employee = document.createElement('td');
                        employee.className = 'py-4 font-medium';
                        employee.textContent = r.employee_name || 'N/A';

                        const region = document.createElement('td');
                        region.className = 'py-4 text-gray-600';
                        region.textContent = r.province_id ? `Province ${r.province_id}` : 'N/A';

                        const date = document.createElement('td');
                        date.className = 'py-4 text-gray-600';
                        if (r.uploaded_at) {
                            const dt = new Date(r.uploaded_at);
                            date.textContent = dt.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
                        } else {
                            date.textContent = 'N/A';
                        }

                        const status = document.createElement('td');
                        status.className = 'py-4';
                        status.innerHTML = `<span class="status-badge bg-green-100 text-green-700">${r.status || 'Submitted'}</span>`;

                        const action = document.createElement('td');
                        action.className = 'py-4';
                        if (r.id) {
                            action.innerHTML = `<a href="/admin/records/${r.id}/download" class="text-gray-400 hover:text-blue-600 transition"><i class="fas fa-download text-lg"></i></a>`;
                        } else {
                            action.textContent = '–';
                            action.classList.add('text-gray-400');
                        }

                        tr.appendChild(employee);
                        tr.appendChild(region);
                        tr.appendChild(date);
                        tr.appendChild(status);
                        tr.appendChild(action);

                        tbody.appendChild(tr);
                    });
                })
                .catch(err => {
                    console.error('Failed to load records:', err);
                    const tbody = document.getElementById('records-table-body');
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-red-500">Error loading records</td></tr>';
                });
        }

        function downloadReport() {
            // Open the report filter modal
            const modal = document.getElementById('report-modal');
            const content = document.getElementById('report-modal-content');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeReportModal() {
            const modal = document.getElementById('report-modal');
            const content = document.getElementById('report-modal-content');
            content.classList.add('scale-95', 'opacity-0');
            content.classList.remove('scale-100', 'opacity-100');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300);
        }

        function loadReportMunicipalities(provinceId) {
            const municipalitySelect = document.getElementById('report-municipality');
            municipalitySelect.innerHTML = '<option value="">All Municipalities</option>';

            if (!provinceId) return;

            const url = `/admin/api/provinces/${provinceId}/municipalities`;
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.json())
                .then(data => {
                    data.forEach(municipality => {
                        const option = document.createElement('option');
                        option.value = municipality.id;
                        option.textContent = municipality.name;
                        municipalitySelect.appendChild(option);
                    });
                });
        }

        function proceedDownloadReport() {
            const province = document.getElementById('report-province').value;
            const municipality = document.getElementById('report-municipality').value;
            const semester = document.getElementById('report-semester').value;
            const year = document.getElementById('report-year').value;

            let url = `/admin/records?province=${province}&municipality=${municipality}`;
            if (semester) url += `&semester=${encodeURIComponent(semester)}`;
            if (year) url += `&year=${encodeURIComponent(year)}`;

            closeReportModal();
            window.location.href = url;
        }

        function showAlert(message = 'Please complete the required fields before proceeding.', type = 'warning') {
            const modal = document.getElementById('alert-modal');
            const content = document.getElementById('alert-content');
            const msgEl = document.getElementById('alert-message');
            const iconEl = modal.querySelector('i');
            const bgEl = modal.querySelector('.w-16');
            
            msgEl.textContent = message;
            
            if (type === 'success') {
                iconEl.className = 'fas fa-check-circle text-green-600 text-2xl';
                bgEl.className = 'w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4';
            } else {
                iconEl.className = 'fas fa-exclamation-triangle text-red-600 text-2xl';
                bgEl.className = 'w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4';
            }
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeAlert() {
            const modal = document.getElementById('alert-modal');
            const content = document.getElementById('alert-content');
            
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300);
        }

        // Close modal on outside click
        document.getElementById('alert-modal').addEventListener('click', function(e) {
            if (e.target === this) closeAlert();
        });

        function toggleNotifications() {
            const dropdown = document.getElementById('notification-dropdown');
            if (!dropdown) return;
            dropdown.classList.toggle('hidden');
            
            // Hide the red notification badge when clicked
            const badge = document.getElementById('notification-badge');
            if (badge) {
                badge.classList.add('hidden');
            }
        }

        // Close notifications dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const btn = document.getElementById('notification-btn');
            const dropdown = document.getElementById('notification-dropdown');
            if (btn && dropdown && !btn.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });

        function loadDashboardRecentSubmissions() {
            fetch('/admin/get_records.php')
                .then(res => res.json())
                .then(response => {
                    const tbody = document.getElementById('dashboard-recent-submissions');
                    tbody.innerHTML = '';
                    
                    if (!response.success || !response.data || response.data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-gray-500">No records found</td></tr>';
                        return;
                    }
                    
                    // Show only the 5 most recent submissions
                    response.data.slice(0, 5).forEach(r => {
                        const tr = document.createElement('tr');
                        tr.className = 'border-b border-gray-100';

                        const employee = document.createElement('td');
                        employee.className = 'py-3 font-medium';
                        employee.textContent = r.employee_name || 'N/A';

                        const region = document.createElement('td');
                        region.className = 'py-3 text-gray-600';
                        region.textContent = r.province_id ? `Province ${r.province_id}` : 'N/A';

                        const date = document.createElement('td');
                        date.className = 'py-3 text-gray-600';
                        if (r.uploaded_at) {
                            const dt = new Date(r.uploaded_at);
                            date.textContent = dt.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
                        } else {
                            date.textContent = 'N/A';
                        }

                        const status = document.createElement('td');
                        status.className = 'py-3';
                        status.innerHTML = `<span class="status-badge bg-green-100 text-green-700">${r.status || 'Submitted'}</span>`;

                        tr.appendChild(employee);
                        tr.appendChild(region);
                        tr.appendChild(date);
                        tr.appendChild(status);

                        tbody.appendChild(tr);
                    });
                })
                .catch(err => {
                    console.error('Failed to load recent submissions:', err);
                    const tbody = document.getElementById('dashboard-recent-submissions');
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-red-500">Error loading records</td></tr>';
                });
        }

        function loadRecordsCount() {
            fetch('/admin/get_records_count.php')
                .then(res => res.json())
                .then(response => {
                    if (response.success) {
                        document.getElementById('uploaded-count').textContent = response.count;
                    }
                })
                .catch(err => {
                    console.error('Failed to load records count:', err);
                });
        }

        function loadStaffCount() {
            fetch('/admin/get_staff_count.php')
                .then(res => res.json())
                .then(response => {
                    if (response.success) {
                        document.getElementById('staff-count').textContent = response.count;
                    }
                })
                .catch(err => {
                    console.error('Failed to load staff count:', err);
                });
        }

        function loadNoticesCount() {
            fetch('/admin/get_notices_count.php')
                .then(res => res.json())
                .then(response => {
                    if (response.success) {
                        document.getElementById('notices-count').textContent = response.count;
                    }
                })
                .catch(err => {
                    console.error('Failed to load notices count:', err);
                });
        }

        // Initialize upload form cascading dropdowns
        document.addEventListener('DOMContentLoaded', function () {

            const provinceSelect = document.getElementById('province-select');
            const municipalitySelect = document.getElementById('municipality-select');
            const schoolSelect = document.querySelector('select[name="school_id"]');
            
            // Load recent submissions on dashboard
            loadDashboardRecentSubmissions();
            loadRecordsCount();
            loadStaffCount();
            loadNoticesCount();

            
            /* ===============================
               LOAD PROVINCES
            =============================== */
            fetch('get_provinces.php')
                .then(res => res.json())
                .then(response => {

                    provinceSelect.innerHTML =
                        '<option value="">Select Province</option>';

                    response.data.forEach(p => {
                        provinceSelect.innerHTML +=
                            `<option value="${p.id}">${p.name}</option>`;
                    });
                })
                .catch(err => console.error('Province error:', err));


            /* ===============================
               PROVINCE → MUNICIPALITIES
            =============================== */
            provinceSelect.addEventListener('change', function () {

                const provinceId = this.value;

                municipalitySelect.innerHTML =
                    '<option>Loading...</option>';

                fetch('get_municipalities.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        province_id: provinceId
                    })
                })
                .then(res => res.json())
                .then(response => {

                    municipalitySelect.innerHTML =
                        '<option value="">Select Municipality</option>';

                    response.data.forEach(m => {
                        municipalitySelect.innerHTML +=
                            `<option value="${m.id}">${m.name}</option>`;
                    });
                });
            });


            /* ===============================
               MUNICIPALITY → SCHOOLS
            =============================== */
            municipalitySelect.addEventListener('change', function () {

                const municipalityId = this.value;
                if (!municipalityId) return;

                schoolSelect.innerHTML =
                    '<option>Loading...</option>';

             fetch('get_schools.php', {
    method: 'POST',
    body: JSON.stringify({ municipality_id: municipalityId })
})
.then(res => res.json())
.then(data => {
    schoolSelect.innerHTML = '<option value="">Select School</option>';
    if (data.success) {
        data.data.forEach(school => {
            schoolSelect.innerHTML += `<option value="${school.id}">${school.name}</option>`;
        });
    }
});
            });

        });

        function showFileName(input) {
            if (input.files.length > 0) {
                document.getElementById('file-name').textContent =
                    input.files[0].name;
            }
        }

        // Cascading dropdowns for records filter
        function loadDashboardMunicipalities(provinceId) {
            // filter select only
            const filterSelect = document.getElementById('filter-municipality');
            if (filterSelect) {
                filterSelect.innerHTML = '<option value="">All Municipalities</option>';
            }

            if (!provinceId) return;

            fetch(`/admin/api/provinces/${provinceId}/municipalities`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(mun => {
                        const option = document.createElement('option');
                        option.value = mun.id;
                        option.textContent = mun.name;
                        if (filterSelect) filterSelect.appendChild(option);
                    });
                });
        }

        // file dropzone interactions
        const dropzone = document.getElementById('file-dropzone');
        if (dropzone) {
            const fileInput = dropzone.querySelector('input[type=file]');
            const fileNameEl = document.getElementById('file-name');

            const updateFilename = () => {
                const f = fileInput.files[0];
                fileNameEl.textContent = f ? f.name : '';
            };

            dropzone.addEventListener('click', () => fileInput.click());
            fileInput.addEventListener('change', updateFilename);

            dropzone.addEventListener('dragover', e => {
                e.preventDefault();
                dropzone.classList.add('border-blue-500');
            });
            dropzone.addEventListener('dragleave', () => {
                dropzone.classList.remove('border-blue-500');
            });
            dropzone.addEventListener('drop', e => {
                e.preventDefault();
                dropzone.classList.remove('border-blue-500');
                if (e.dataTransfer.files.length) {
                    fileInput.files = e.dataTransfer.files;
                    updateFilename();
                }
            });
        }

        @if(old('role'))
            document.addEventListener('DOMContentLoaded', () => {
                selectedRole = "{{ old('role') }}";
                document.getElementById('selected-role').textContent = selectedRole;
                document.getElementById('confirm-role').textContent = selectedRole;
                document.getElementById('hidden-role').value = selectedRole;
            });
        @endif
        @if(old('province_id'))
            document.addEventListener('DOMContentLoaded', () => {
                const prov = "{{ old('province_id') }}";
                loadDashboardMunicipalities(prov);
                // once municipalities loaded set old municipality
                const checkInterval = setInterval(() => {
                    const mun = document.getElementById('municipality-select');
                    if (mun && mun.options.length > 1) {
                        mun.value = "{{ old('municipality_id') }}";
                        clearInterval(checkInterval);
                    }
                }, 50);
            });
        @endif
    </script>

    <!-- LOGOUT CONFIRMATION MODAL -->
    <div x-show="showLogoutModal"
         x-transition
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-2xl p-6 w-96 text-center">
            <i class="fas fa-sign-out-alt text-3xl text-red-600 mb-4 block"></i>
            <h3 class="text-lg font-semibold mb-2">Confirm Logout</h3>
            <p class="text-sm text-gray-500 mb-6">Are you sure you want to sign out?</p>

            <div class="flex gap-3 justify-center">
                <button @click="showLogoutModal=false"
                        class="px-4 py-2 rounded-lg border text-gray-600 hover:bg-gray-100 transition">
                    Cancel
                </button>

                <button @click="
                    showLogoutModal=false;
                    loggingOut=true;
                    clearUserData();
                    setTimeout(() => document.getElementById('logoutForm').submit(), 1200);
                "
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    Logout
                </button>
            </div>
        </div>
    </div>

    <!-- LOGGING OUT ANIMATION -->
    <div x-show="loggingOut"
         x-transition.opacity
         class="fixed inset-0 bg-white flex flex-col items-center justify-center z-50">
        <div class="animate-spin rounded-full h-16 w-16 border-4 border-blue-600 border-t-transparent mb-6"></div>
        <h2 class="text-xl font-semibold text-blue-600">Logging out...</h2>
        <p class="text-gray-500 text-sm">Please wait</p>
    </div>

    <!-- Hidden Logout Form -->
    <form id="logoutForm" method="POST" action="{{ route('logout') }}" style="display: none;">
        @csrf
    </form>

    <script>
        function clearUserData() {
            localStorage.removeItem('user');
            localStorage.removeItem('rememberMe');
            sessionStorage.removeItem('user');
        }
    </script>
@endsection
