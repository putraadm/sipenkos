<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RekapPendapatan;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportPendapatanController extends Controller
{
    use ApiResponse;
    public function getLaporan(Request $request)
    {
        $query = RekapPendapatan::filter($request->all());

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
        
        $dataTabel = ($request->query('cetak') == 'true') 
            ? $query->get() 
            : $query->paginate(15);

        return $this->successResponse([
            'agregasi' => [
                'hari_ini'    => (float) $pemasukanHariIni,
                'bulan_ini'   => (float) $pemasukanBulanIni,
                'tahun_ini'   => (float) $pemasukanTahunIni,
                'keseluruhan' => (float) $pemasukanKeseluruhan
            ],
            'laporan' => $dataTabel
        ], 'Data laporan pendapatan berhasil diambil.');
    }
}
