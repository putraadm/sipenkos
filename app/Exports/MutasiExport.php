<?php

namespace App\Exports;

use App\Models\RiwayatMutasi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class MutasiExport extends DefaultValueBinder implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithColumnFormatting, WithCustomValueBinder
{
    protected $mutasi;

    public function __construct($mutasi)
    {
        $this->mutasi = $mutasi;
    }

    public function bindValue(Cell $cell, $value)
    {
        if (is_numeric($value) && strlen((string)$value) >= 10) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
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
            'NIK',
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
            $mutasi->penghuni?->nik ?? '-',
            $mutasi->penghuni?->no_wa ?? '-',
            $mutasi->kos?->nama_kos ?? '-',
            $mutasi->kos?->nama_pemilik ?? '-',
            ucfirst($mutasi->jenis_mutasi),
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '3B82F6']]],
            'A1:H1' => ['alignment' => ['horizontal' => 'center']],
        ];
    }
}
