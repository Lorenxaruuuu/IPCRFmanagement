<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Admin\IpcrfController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\PerformanceController;
use App\Http\Admin\NoticeController;
use App\Http\Admin\FormController;
use App\Http\Controllers\GoogleDriveAuthController;

Route::get('/admins', function () {
    return redirect()->route('admin.dashboard');
});

Route::get('/notifications2', function () {
    return view('notifications_page');
})->name('notifications');


Route::get('/performance', [PerformanceController::class, 'index'])->name('performance.index');
    Route::get('/performance/{id}/view', [PerformanceController::class, 'show'])->name('performance.show');
    Route::get('/performance/download-report', [PerformanceController::class, 'downloadReport'])->name('performance.download');Route::get('/forms', [FormController::class, 'index'])->name('forms.index');
// download a single form; use numeric id since we have no database
Route::get('/forms/download/{id}', [FormController::class, 'download'])->name('forms.download');
Route::get('/', function () {
    return view('userDashboard');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/home', function () {
    return view('userDashboard');
})->name('userDashboard');

Route::get('/encoder', [IpcrfController::class, 'index'])->name('dashboards');
Route::get('/list', [IpcrfController::class, 'showList'])->name('ipcrf.list');
Route::get('/upload', [IpcrfController::class, 'create'])->name('upload.create');
Route::post('/upload', [IpcrfController::class, 'store'])->name('upload.store');

Route::prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [IpcrfController::class, 'dashboard'])->name('dashboard');
    
    // Upload - Single page, no steps
    Route::get('/upload', [IpcrfController::class, 'uploadForm'])->name('upload');
    Route::post('/upload', [IpcrfController::class, 'store2'])->name('upload.store');
    
    // Records
    Route::get('/records', [IpcrfController::class, 'records'])->name('records');
    Route::get('/records/{id}/download', [IpcrfController::class, 'download'])->name('records.download');
    
    // API for cascading dropdowns (controller-based)
    Route::get('/api/provinces/{province}/municipalities', [IpcrfController::class, 'getMunicipalities']);
    Route::get('/api/municipalities/{municipality}/schools', [IpcrfController::class, 'getSchools']);
    
    // Notices
    Route::get('/notices', [NoticeController::class, 'index'])->name('notices');
    Route::post('/notices', [NoticeController::class, 'store'])->name('notices.store');
    Route::delete('/notices/{id}', [NoticeController::class, 'destroy'])->name('notices.destroy');
    
    // Forms
    Route::get('/forms', [FormController::class, 'index'])->name('forms');
    Route::post('/forms', [FormController::class, 'store'])->name('forms.store');

    Route::get('/forms/{id}/download', [FormController::class, 'download2'])->name('forms.download');
    Route::delete('/forms/{id}', [FormController::class, 'destroy'])->name('forms.destroy');
    
    // Google Drive Authorization
    Route::get('/settings/google-drive/authorize', [GoogleDriveAuthController::class, 'authorize'])->name('gdrive.authorize');
});

// Google Drive OAuth Callback (outside admin group)
Route::get('/auth/google/callback', [GoogleDriveAuthController::class, 'callback'])->name('gdrive.callback');

Route::post('/notifications/mark-all-read', function () {
    DB::table('notifications')
        ->whereNull('read_at')
        ->update(['read_at' => now()]);
    return back();
})->name('notifications.markAllAsRead');

Route::get('/notifications', function () {
    return view('notifications_page');
})->name('notifications.index');

// lightweight API endpoints (no controller)
