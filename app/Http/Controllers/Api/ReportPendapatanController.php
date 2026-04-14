<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ReportPendapatanController extends Controller
{
    use ApiResponse;

    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function getLaporan(Request $request)
    {
        try {
            $isCetak = $request->query('cetak') === 'true';
            $data = $this->reportService->getPendapatanReport($request->all(), $isCetak);

            return $this->successResponse($data, 'Data laporan pendapatan berhasil diambil.', 200);
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal mengambil laporan: ' . $e->getMessage(), 500);
        }
    }
}
