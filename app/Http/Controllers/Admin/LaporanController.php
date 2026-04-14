<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MutasiService;
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
    protected $mutasiService;

    public function __construct(MutasiService $mutasiService)
    {
        $this->mutasiService = $mutasiService;
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        if ($user && $user->role && $user->role->name === 'pemilik_kos') {
            return redirect()->route('pemilik.dashboard');
        }

        $perPage = $request->input('per_page', 10);
        
        $stats = $this->mutasiService->getMutasiStats($request->all());
        $mutasi = $this->mutasiService->getMutasiReport($request->all())
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('admin/laporan/index', [
            'mutasi' => $mutasi,
            'filters' => $request->only('search', 'month', 'year', 'jenis_mutasi', 'sort_by', 'sort_direction'),
            'stats' => $stats
        ]);
    }

    public function exportPdf(Request $request)
    {
        $data = $this->mutasiService->getMutasiReport($request->all())->get();
        $stats = $this->mutasiService->getMutasiStats($request->all());

        $pdf = Pdf::loadView('admin.laporan.pdf', [
            'data' => $data,
            'stats' => $stats,
            'filters' => $request->only('month', 'year')
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('Laporan-Mutasi-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $data = $this->mutasiService->getMutasiReport($request->all())->get();
        return Excel::download(new MutasiExport($data), 'Laporan-Mutasi-' . now()->format('Y-m-d') . '.xlsx');
    }
}