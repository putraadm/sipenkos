<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RiwayatMutasi;
use App\Exports\MutasiExport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);
        $month = $request->input('month');
        $year = $request->input('year');
        $jenisMutasi = $request->input('jenis_mutasi');
        $sortBy = $request->input('sort_by', 'tanggal_mutasi');
        $sortDirection = $request->input('sort_direction', 'asc');

        $now = Carbon::now();
        $masukBulanIni = RiwayatMutasi::where('jenis_mutasi', 'masuk')
            ->whereMonth('tanggal_mutasi', $now->month)
            ->whereYear('tanggal_mutasi', $now->year)
            ->count();
        
        $keluarBulanIni = RiwayatMutasi::where('jenis_mutasi', 'keluar')
            ->whereMonth('tanggal_mutasi', $now->month)
            ->whereYear('tanggal_mutasi', $now->year)
            ->count();

        $totalPenghuniAktif = RiwayatMutasi::select('penghuni_id')
            ->whereIn('id', function($query) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('riwayat_mutasi')
                    ->groupBy('penghuni_id');
            })
            ->where('jenis_mutasi', 'masuk')
            ->count();

        $mutasi = $this->getBaseQuery($request)
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('admin/laporan/index', [
            'mutasi' => $mutasi,
            'filters' => $request->only('search', 'month', 'year', 'jenis_mutasi', 'sort_by', 'sort_direction'),
            'stats' => [
                'total_penghuni_aktif' => $totalPenghuniAktif,
                'masuk_bulan_ini' => $masukBulanIni,
                'keluar_bulan_ini' => $keluarBulanIni,
                'current_month' => $now->translatedFormat('F'),
                'current_year' => $now->year,
            ]
        ]);
    }

    private function getBaseQuery(Request $request)
    {
        $search = $request->input('search');
        $month = $request->input('month');
        $year = $request->input('year');
        $jenisMutasi = $request->input('jenis_mutasi');
        $sortBy = $request->input('sort_by', 'tanggal_mutasi');
        $sortDirection = $request->input('sort_direction', 'asc');

        return RiwayatMutasi::with([
            'kos:kos_id,nama_kos,nama_pemilik',
            'penghuni:penghuni_id,nama,no_wa,file_ktp,file_kk'
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

    public function exportPdf(Request $request)
    {
        $data = $this->getBaseQuery($request)->get();
        $now = Carbon::now();
        
        $masukBulanIni = RiwayatMutasi::where('jenis_mutasi', 'masuk')->whereMonth('tanggal_mutasi', $now->month)->whereYear('tanggal_mutasi', $now->year)->count();
        $keluarBulanIni = RiwayatMutasi::where('jenis_mutasi', 'keluar')->whereMonth('tanggal_mutasi', $now->month)->whereYear('tanggal_mutasi', $now->year)->count();
        $totalPenghuniAktif = RiwayatMutasi::select('penghuni_id')->whereIn('id', function($query) {
                $query->select(DB::raw('MAX(id)'))->from('riwayat_mutasi')->groupBy('penghuni_id');
            })->where('jenis_mutasi', 'masuk')->count();

        $stats = [
            'total_penghuni_aktif' => $totalPenghuniAktif,
            'masuk_bulan_ini' => $masukBulanIni,
            'keluar_bulan_ini' => $keluarBulanIni,
            'current_month' => $now->translatedFormat('F'),
        ];

        $pdf = Pdf::loadView('admin.laporan.pdf', [
            'data' => $data,
            'stats' => $stats,
            'filters' => $request->only('month', 'year')
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('Laporan-Mutasi-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $data = $this->getBaseQuery($request)->get();
        return Excel::download(new MutasiExport($data), 'Laporan-Mutasi-' . now()->format('Y-m-d') . '.xlsx');
    }
}
