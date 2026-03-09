<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LaporanController;

Route::get('/', function () {
    return redirect()->route('login');
});

// ... entries

Route::get('dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/laporan-mutasi', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan-mutasi/pdf', [LaporanController::class, 'exportPdf'])->name('laporan.pdf');
    Route::get('/laporan-mutasi/excel', [LaporanController::class, 'exportExcel'])->name('laporan.excel');
});

require __DIR__.'/settings.php';
