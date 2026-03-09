<?php

namespace App\Exports;

use App\Models\RiwayatMutasi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MutasiExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $mutasi;

    public function __construct($mutasi)
    {
        $this->mutasi = $mutasi;
    }

    public function collection()
    {
        return $this->mutasi;
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal Mutasi',
            'Nama Penghuni',
            'No. WA',
            'Nama Kos',
            'Pemilik Kos',
            'Jenis Mutasi',
        ];
    }

    public function map($mutasi): array
    {
        static $no = 1;
        return [
            $no++,
            $mutasi->tanggal_mutasi,
            $mutasi->penghuni?->nama ?? '-',
            $mutasi->penghuni?->no_wa ?? '-',
            $mutasi->kos?->nama_kos ?? '-',
            $mutasi->kos?->nama_pemilik ?? '-',
            ucfirst($mutasi->jenis_mutasi),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '3B82F6']]],
            'A1:G1' => ['alignment' => ['horizontal' => 'center']],
        ];
    }
}
