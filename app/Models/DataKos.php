<?php

namespace App\Models;

use App\Models\RiwayatMutasi;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataKos extends Model
{
    use HasFactory;

    protected $table = 'data_kos';

    protected $fillable = [
        'kos_id',
        'pemilik_id',
        'nama_pemilik',
        'no_wa_pemilik',
        'nama_kos',
        'alamat',
    ];

    public function riwayatMutasi()
    {
        return $this->hasMany(RiwayatMutasi::class, 'kos_id', 'kos_id');
    }
}
