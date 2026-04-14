<?php

namespace App\Services;

use App\Models\RekapPendapatan;
use Carbon\Carbon;

class ReportService
{
    public function getPendapatanReport(array $filters, bool $isCetak = false)
    {
        $query = RekapPendapatan::filter($filters);

        $today = Carbon::today();
        $thisMonth = Carbon::now()->month;
        $thisYear = Carbon::now()->year;

        $baseQuery = clone $query;

        $pemasukanHariIni = (clone $baseQuery)->whereDate('tanggal_pembayaran', $today)->sum('nominal');
        $pemasukanBulanIni = (clone $baseQuery)->whereMonth('tanggal_pembayaran', $thisMonth)
                                               ->whereYear('tanggal_pembayaran', $thisYear)->sum('nominal');
        $pemasukanTahunIni = (clone $baseQuery)->whereYear('tanggal_pembayaran', $thisYear)->sum('nominal');
        $pemasukanKeseluruhan = (clone $baseQuery)->sum('nominal');

        $query->orderBy('tanggal_pembayaran', 'desc');
        
        $dataTabel = $isCetak ? $query->get() : $query->paginate(15);

        return [
            'agregasi' => [
                'hari_ini'    => (float) $pemasukanHariIni,
                'bulan_ini'   => (float) $pemasukanBulanIni,
                'tahun_ini'   => (float) $pemasukanTahunIni,
                'keseluruhan' => (float) $pemasukanKeseluruhan
            ],
            'laporan' => $dataTabel
        ];
    }
}
