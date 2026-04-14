<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SyncController;
use App\Http\Controllers\Api\ReportPendapatanController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::post('/sync/kos', [SyncController::class, 'syncKos']);
    Route::post('/sync/mutasi', [SyncController::class, 'syncMutasi']);
    Route::post('/sync/penghuni', [SyncController::class, 'syncPenghuni']);
    Route::post('/sync/pendapatan', [SyncController::class, 'syncPendapatan']);
    Route::get('/laporan/pendapatan', [ReportPendapatanController::class, 'getLaporan']);
});
