<?php

namespace App\Services;

use App\Models\DataKos;
use App\Models\DataPenghuni;
use App\Models\LaporanPemilikKos;
use App\Models\RiwayatMutasi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Get statistics and chart data for the admin dashboard.
     */
    public function getAdminDashboardData()
    {
        // Digital/Synced Data
        $totalPenghuniDigital = DataPenghuni::count();
        $totalKosDigital = DataKos::count();
        
        $now = Carbon::now();
        $mutasiBulanIni = RiwayatMutasi::whereMonth('tanggal_mutasi', $now->month)
            ->whereYear('tanggal_mutasi', $now->year)
            ->count();

        // Manual/Reported Data (Latest report for each owner)
        $latestManualReports = LaporanPemilikKos::whereIn('id', function($query) {
                $query->selectRaw('MAX(id)')
                    ->from('laporan_pemilik_kos')
                    ->groupBy('user_id');
            })->get();

        $totalPenghuniManual = $latestManualReports->sum('jumlah_penghuni');
        $totalKosManual = $latestManualReports->sum('jumlah_kos');

        // Data for Chart (Last 6 Months - Digital only)
        $chartData = $this->getChartData($now);

        $recentActivities = RiwayatMutasi::with(['penghuni', 'kos'])
            ->latest()
            ->limit(3)
            ->get();

        return [
            'stats' => [
                'total_penghuni_digital' => $totalPenghuniDigital,
                'total_kos_digital' => $totalKosDigital,
                'total_penghuni_manual' => $totalPenghuniManual,
                'total_kos_manual' => $totalKosManual,
                'mutasi_bulan_ini' => $mutasiBulanIni,
            ],
            'chartData' => $chartData,
            'recentActivities' => $recentActivities
        ];
    }


    /**
     * Calculate chart data for the last 6 months.
     */
    private function getChartData(Carbon $now)
    {
        $sixMonthsAgo = $now->copy()->subMonths(5)->startOfMonth();
        
        // Optimize: Single query to get all mutation counts for the last 6 months
        $mutasiCounts = RiwayatMutasi::selectRaw('YEAR(tanggal_mutasi) as year, MONTH(tanggal_mutasi) as month, jenis_mutasi, count(*) as count')
            ->where('tanggal_mutasi', '>=', $sixMonthsAgo)
            ->groupBy('year', 'month', 'jenis_mutasi')
            ->get()
            ->groupBy(function($item) {
                return $item->year . '-' . $item->month;
            });

        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthDate = $now->copy()->subMonths($i);
            $key = $monthDate->year . '-' . $monthDate->month;
            
            $counts = $mutasiCounts->get($key, collect());
            
            $chartData[] = [
                'name' => $monthDate->translatedFormat('M'),
                'masuk' => (int) ($counts->where('jenis_mutasi', 'masuk')->first()?->count ?? 0),
                'keluar' => (int) ($counts->where('jenis_mutasi', 'keluar')->first()?->count ?? 0),
            ];
        }
        return $chartData;
    }
}

