<?php

namespace App\Services;

use App\Models\DataPenghuni;

class PenghuniService
{
    /**
     * Track a resident based on search query.
     */
    public function trackPenghuni(?string $search)
    {
        if (!$search) {
            return null;
        }

        return DataPenghuni::with(['riwayatMutasi' => function($q) {
                $q->orderBy('tanggal_mutasi', 'desc');
            }, 'riwayatMutasi.kos'])
            ->where('nama', 'like', '%' . $search . '%')
            ->orWhere('penghuni_id', 'like', '%' . $search . '%')
            ->orWhere('nik', 'like', '%' . $search . '%')
            ->first();
    }
}
