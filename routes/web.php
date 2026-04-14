<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\TrackingPenghuniController;
use App\Http\Controllers\Admin\KelolaUserController;
use App\Http\Controllers\PemilikKos\DashboardController as PemilikKosDashboardController;
use App\Http\Controllers\PemilikKos\ProfileController as PemilikKosProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// ... entries

Route::get('dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/tracking-penghuni', [TrackingPenghuniController::class, 'index'])->name('tracking.penghuni');
    
    // Kelola User
    Route::get('/admin/user', [KelolaUserController::class, 'index'])->name('admin.user.index');
    Route::post('/admin/user', [KelolaUserController::class, 'store'])->name('admin.user.store');
    Route::put('/admin/user/{id}', [KelolaUserController::class, 'update'])->name('admin.user.update');
    Route::delete('/admin/user/{id}', [KelolaUserController::class, 'destroy'])->name('admin.user.destroy');
    
    Route::get('/laporan-mutasi', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan-mutasi/pdf', [LaporanController::class, 'exportPdf'])->name('laporan.pdf');
    Route::get('/laporan-mutasi/excel', [LaporanController::class, 'exportExcel'])->name('laporan.excel');
    
    // Pemilik Kos
    Route::get('/pemilik-kos/dashboard', [PemilikKosDashboardController::class, 'index'])->name('pemilik.dashboard');
    Route::post('/pemilik-kos/report', [PemilikKosDashboardController::class, 'store'])->name('pemilik.report.store');
    
    Route::get('/pemilik-kos/profile/edit', [PemilikKosProfileController::class, 'edit'])->name('pemilik.profile.edit');
    Route::put('/pemilik-kos/profile/update', [PemilikKosProfileController::class, 'update'])->name('pemilik.profile.update');
});

require __DIR__.'/settings.php';
