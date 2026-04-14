<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataPenghuni;
use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Services\PenghuniService;

class TrackingPenghuniController extends Controller
{
    protected $penghuniService;

    public function __construct(PenghuniService $penghuniService)
    {
        $this->penghuniService = $penghuniService;
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        if ($user && $user->role && $user->role->name === 'pemilik_kos') {
            return redirect()->route('pemilik.dashboard');
        }

        $search = $request->input('search');
        $penghuni = $this->penghuniService->trackPenghuni($search);

        return Inertia::render('admin/tracking-penghuni/page', [
            'penghuni' => $penghuni,
            'searchQuery' => $search
        ]);
    }
}

