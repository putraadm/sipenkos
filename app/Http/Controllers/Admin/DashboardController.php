<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataKos;
use App\Models\DataPenghuni;
use App\Models\RiwayatMutasi;
use Carbon\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPenghuni = DataPenghuni::count();
        $totalKos = DataKos::count();
        
        $now = Carbon::now();
        $mutasiBulanIni = RiwayatMutasi::whereMonth('tanggal_mutasi', $now->month)
            ->whereYear('tanggal_mutasi', $now->year)
            ->count();

        // Data for Chart (Last 6 Months)
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $masuk = RiwayatMutasi::whereMonth('tanggal_mutasi', $month->month)
                ->whereYear('tanggal_mutasi', $month->year)
                ->where('jenis_mutasi', 'masuk')
                ->count();
            
            $keluar = RiwayatMutasi::whereMonth('tanggal_mutasi', $month->month)
                ->whereYear('tanggal_mutasi', $month->year)
                ->where('jenis_mutasi', 'keluar')
                ->count();

            $chartData[] = [
                'name' => $month->translatedFormat('M'),
                'masuk' => $masuk,
                'keluar' => $keluar,
            ];
        }

        $recentActivities = RiwayatMutasi::with(['penghuni', 'kos'])
            ->latest()
            ->limit(3)
            ->get();

        return Inertia::render('dashboard', [
            'stats' => [
                'total_penghuni' => $totalPenghuni,
                'total_kos' => $totalKos,
                'mutasi_bulan_ini' => $mutasiBulanIni,
            ],
            'chartData' => $chartData,
            'recentActivities' => $recentActivities
        ]);
    }
}
