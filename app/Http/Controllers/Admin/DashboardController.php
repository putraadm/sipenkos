<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataKos;
use App\Models\DataPenghuni;
use App\Models\RiwayatMutasi;
use Carbon\Carbon;
use Inertia\Inertia;

use App\Services\DashboardService;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        $user = auth()->user();
        if ($user && $user->role && $user->role->name === 'pemilik_kos') {
            return redirect()->route('pemilik.dashboard');
        }

        $data = $this->dashboardService->getAdminDashboardData();

        return Inertia::render('dashboard', $data);
    }
}

