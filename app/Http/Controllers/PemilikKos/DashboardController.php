<?php

namespace App\Http\Controllers\PemilikKos;

use App\Http\Controllers\Controller;
use App\Models\LaporanPemilikKos;
use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Services\LaporanPemilikService;

class DashboardController extends Controller
{
    protected $laporanService;

    public function __construct(LaporanPemilikService $laporanService)
    {
        $this->laporanService = $laporanService;
    }

    public function index()
    {
        $user = auth()->user();
        $laporans = $this->laporanService->getLaporanByOwner($user);

        return Inertia::render('pemilik-kos/dashboard', [
            'laporans' => $laporans
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'jumlah_kos' => 'required|integer|min:0',
            'jumlah_penghuni' => 'required|integer|min:0',
            'periode_bulan' => 'required|integer|min:1|max:12',
            'periode_tahun' => 'required|integer|min:2000',
        ]);

        $user = auth()->user();

        try {
            $this->laporanService->storeLaporan($user, $request->all());
            return redirect()->back()->with('success', 'Laporan berhasil disimpan.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['message' => $e->getMessage()]);
        }
    }
}

