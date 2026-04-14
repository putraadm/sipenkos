<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanPemilikKos extends Model
{
    protected $table = 'laporan_pemilik_kos';

    protected $fillable = [
        'user_id',
        'jumlah_kos',
        'jumlah_penghuni',
        'periode_bulan',
        'periode_tahun',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
