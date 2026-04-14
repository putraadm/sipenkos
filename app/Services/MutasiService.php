<?php

namespace App\Services;

use App\Models\RiwayatMutasi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class MutasiService
{
    /**
     * Get mutasi data for reports with filtering and sorting.
     */
    public function getMutasiReport(array $filters)
    {
        $search = $filters['search'] ?? null;
        $month = $filters['month'] ?? null;
        $year = $filters['year'] ?? null;
        $jenisMutasi = $filters['jenis_mutasi'] ?? null;
        $sortBy = $filters['sort_by'] ?? 'tanggal_mutasi';
        $sortDirection = $filters['sort_direction'] ?? 'asc';

        return RiwayatMutasi::with([
            'kos:kos_id,nama_kos,nama_pemilik',
            'penghuni:penghuni_id,nik,nama,no_wa,file_ktp,file_kk'
        ])
            ->search($search)
            ->when($month, fn($q) => $q->whereMonth('tanggal_mutasi', $month))
            ->when($year, fn($q) => $q->whereYear('tanggal_mutasi', $year))
            ->when($jenisMutasi, fn($q) => $q->where('jenis_mutasi', $jenisMutasi))
            ->when($sortBy === 'penghuni.nama', function ($q) use ($sortDirection) {
                $q->join('data_penghuni', 'riwayat_mutasi.penghuni_id', '=', 'data_penghuni.penghuni_id')
                    ->orderBy('data_penghuni.nama', $sortDirection)
                    ->select('riwayat_mutasi.*');
            })
            ->when($sortBy === 'kos.nama_kos', function ($q) use ($sortDirection) {
                $q->join('data_kos', 'riwayat_mutasi.kos_id', '=', 'data_kos.kos_id')
                    ->orderBy('data_kos.nama_kos', $sortDirection)
                    ->select('riwayat_mutasi.*');
            })
            ->when(in_array($sortBy, ['tanggal_mutasi', 'jenis_mutasi']), function ($q) use ($sortBy, $sortDirection) {
                $q->orderBy($sortBy, $sortDirection);
            }, function ($q) use ($sortBy) {
                if (!in_array($sortBy, ['penghuni.nama', 'kos.nama_kos', 'tanggal_mutasi', 'jenis_mutasi'])) {
                    $q->latest('tanggal_mutasi')->latest();
                }
            });
    }

    /**
     * Get summary statistics for mutasi.
     */
    public function getMutasiStats(array $filters = [])
    {
        $month = $filters['month'] ?? null;
        $year = $filters['year'] ?? null;
        $now = Carbon::now();
        
        $masukCount = RiwayatMutasi::where('jenis_mutasi', 'masuk')
            ->when($month, fn($q) => $q->whereMonth('tanggal_mutasi', $month))
            ->when($year, fn($q) => $q->whereYear('tanggal_mutasi', $year))
            ->count();
        
        $keluarCount = RiwayatMutasi::where('jenis_mutasi', 'keluar')
            ->when($month, fn($q) => $q->whereMonth('tanggal_mutasi', $month))
            ->when($year, fn($q) => $q->whereYear('tanggal_mutasi', $year))
            ->count();

        // Create a period label
        $periodLabel = 'Semua Waktu';
        if ($month && $year) {
            try {
                $periodLabel = Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y');
            } catch (\Exception $e) {
                $periodLabel = "$month/$year";
            }
        } elseif ($year) {
            $periodLabel = "Tahun $year";
        } elseif ($month) {
            try {
                $periodLabel = Carbon::createFromDate(null, $month, 1)->translatedFormat('F');
            } catch (\Exception $e) {
                $periodLabel = "Bulan $month";
            }
        }

        // Optimize: Get latest mutation for each resident to count active ones (jenis_mutasi = 'masuk')
        $totalPenghuniAktif = RiwayatMutasi::whereIn('id', function($query) {
                $query->selectRaw('MAX(id)')
                    ->from('riwayat_mutasi')
                    ->groupBy('penghuni_id');
            })
            ->where('jenis_mutasi', 'masuk')
            ->count();


        return [
            'total_penghuni_aktif' => $totalPenghuniAktif,
            'masuk_count' => $masukCount,
            'keluar_count' => $keluarCount,
            'period_label' => $periodLabel,
            'current_month' => $now->translatedFormat('F'),
            'current_year' => $now->year,
        ];
    }
}
