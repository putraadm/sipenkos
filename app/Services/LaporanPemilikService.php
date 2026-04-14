<?php

namespace App\Services;

use App\Models\LaporanPemilikKos;
use App\Models\User;

class LaporanPemilikService
{
    /**
     * Get reports for a specific owner.
     */
    public function getLaporanByOwner(User $user)
    {
        return LaporanPemilikKos::where('user_id', $user->id)
            ->orderBy('periode_tahun', 'desc')
            ->orderBy('periode_bulan', 'desc')
            ->get();
    }

    /**
     * Store a new report for an owner.
     */
    public function storeLaporan(User $user, array $data)
    {
        // Check if report already exists for this period
        $exists = LaporanPemilikKos::where('user_id', $user->id)
            ->where('periode_bulan', $data['periode_bulan'])
            ->where('periode_tahun', $data['periode_tahun'])
            ->exists();

        if ($exists) {
            throw new \Exception('Laporan untuk bulan/tahun ini sudah ada.');
        }

        return LaporanPemilikKos::create([
            'user_id' => $user->id,
            'jumlah_kos' => $data['jumlah_kos'],
            'jumlah_penghuni' => $data['jumlah_penghuni'],
            'periode_bulan' => $data['periode_bulan'],
            'periode_tahun' => $data['periode_tahun'],
        ]);
    }
}
