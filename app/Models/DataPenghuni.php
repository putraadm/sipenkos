<?php

namespace App\Models;

use App\Models\RiwayatMutasi;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataPenghuni extends Model
{
    use HasFactory;

    protected $table = 'data_penghuni';

    protected $fillable = [
        'penghuni_id',
        'nik',
        'nama',
        'no_wa',
        'agama',
        'file_ktp',
        'file_kk'
    ];

    public function riwayatMutasi()
    {
        return $this->hasMany(RiwayatMutasi::class, 'penghuni_id', 'penghuni_id');
    }
}
