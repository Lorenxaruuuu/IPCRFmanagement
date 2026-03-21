<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IpcrfController;


Route::get('/', [IpcrfController::class, 'index'])->name('dashboard');
Route::get('/list', [IpcrfController::class, 'showList'])->name('ipcrf.list');
Route::get('/upload', [IpcrfController::class, 'create'])->name('upload.create');
Route::post('/upload', [IpcrfController::class, 'store'])->name('upload.store');
Route::get('/ipcrf/{id}', [IpcrfController::class, 'show'])->name('ipcrf.show');
Route::get('/ipcrf/{id}/document', [IpcrfController::class, 'document'])->name('ipcrf.document');