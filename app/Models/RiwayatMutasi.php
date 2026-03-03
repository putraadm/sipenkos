<?php

namespace App\Models;

use App\Models\DataKos;
use App\Models\DataPenghuni;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatMutasi extends Model
{
    use HasFactory;

    protected $table = 'riwayat_mutasi';

    protected $fillable = [
        'kos_id',
        'penghuni_id',
        'jenis_mutasi',
        'tanggal_mutasi'
    ];

    public function kos()
    {
        return $this->belongsTo(DataKos::class, 'kos_id', 'kos_id');
    }

    public function penghuni()
    {
        return $this->belongsTo(DataPenghuni::class, 'penghuni_id', 'penghuni_id');
    }

    /**
     * Scope a query to search reports.
     */
    public function scopeSearch($query, $search)
    {
        if (!$search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->whereHas('penghuni', function ($sq) use ($search) {
                $sq->where('nama', 'like', "%{$search}%");
            })->orWhereHas('kos', function ($sq) use ($search) {
                $sq->where('nama_kos', 'like', "%{$search}%");
            });
        });
    }
}
