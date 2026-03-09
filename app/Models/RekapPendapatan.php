<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekapPendapatan extends Model
{
    use HasFactory;

    protected $table = 'rekap_pendapatan';
    protected $fillable = [
        'kos_id',
        'pemilik_id',
        'nama_kos',
        'nama_penghuni',
        'nomor_kamar',
        'tipe_kamar',
        'periode_tagihan',
        'metode_pembayaran',
        'nominal',
        'tanggal_pembayaran',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'tanggal_pembayaran' => 'datetime',
    ];

    public function kos()
    {
        return $this->belongsTo(DataKos::class, 'kos_id', 'kos_id');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['pemilik_id'] ?? false, function ($q, $pemilikId) {
            return $q->where('pemilik_id', $pemilikId);
        });

        $query->when($filters['kos_id'] ?? false, function ($q, $kosId) {
            return $q->where('kos_id', $kosId);
        });

        $query->when($filters['bulan'] ?? false, function ($q, $bulan) {
            return $q->whereMonth('tanggal_pembayaran', $bulan);
        });

        $query->when($filters['tahun'] ?? false, function ($q, $tahun) {
            return $q->whereYear('tanggal_pembayaran', $tahun);
        });

        $query->when($filters['metode'] ?? false, function ($q, $metode) {
            return $q->where('metode_pembayaran', $metode);
        });

        $query->when($filters['search'] ?? false, function ($q, $search) {
            return $q->where(function($query) use ($search) {
                $query->where('nama_penghuni', 'like', "%{$search}%")
                      ->orWhere('nomor_kamar', 'like', "%{$search}%");
            });
        });
    }

}