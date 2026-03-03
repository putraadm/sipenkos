<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RiwayatMutasi;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);
        $month = $request->input('month');
        $year = $request->input('year');
        $jenisMutasi = $request->input('jenis_mutasi');

        $mutasi = RiwayatMutasi::with([
            'kos:kos_id,nama_kos,nama_pemilik',
            'penghuni:penghuni_id,nama,no_wa,file_ktp,file_kk'
        ])
            ->search($search)
            ->when($month, fn($q) => $q->whereMonth('tanggal_mutasi', $month))
            ->when($year, fn($q) => $q->whereYear('tanggal_mutasi', $year))
            ->when($jenisMutasi, fn($q) => $q->where('jenis_mutasi', $jenisMutasi))
            ->latest('tanggal_mutasi')
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('admin/laporan/index', [
            'mutasi' => $mutasi,
            'filters' => $request->only('search', 'month', 'year', 'jenis_mutasi')
        ]);
    }
}
