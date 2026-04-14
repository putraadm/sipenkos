<?php

namespace App\Services;

use App\Models\DataKos;
use App\Models\DataPenghuni;
use App\Models\RekapPendapatan;
use App\Models\RiwayatMutasi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Carbon\Carbon;

class SyncService
{
    public function syncKos(array $data)
    {
        return DataKos::updateOrCreate(
            ['kos_id' => $data['id_kos']],
            [
                'pemilik_id' => $data['id_pemilik'],
                'nama_pemilik' => $data['nama_pemilik'],
                'no_wa_pemilik' => $data['no_wa_pemilik'],
                'nama_kos'   => $data['nama_kos'],
                'alamat'     => $data['alamat'],
            ]
        );
    }

    public function syncMutasi(array $data, $fileKtp, $fileKk)
    {
        DB::beginTransaction();
        try {
            $existingPenghuni = DataPenghuni::where('penghuni_id', $data['id_penghuni'])->first();

            if (!$existingPenghuni && !empty($data['nik'])) {
                $existingPenghuni = DataPenghuni::where('nik', $data['nik'])->first();
            }

            $pathKtp = $this->handleImageUpload($fileKtp, 'ktp', optional($existingPenghuni)->file_ktp);
            $pathKk = $this->handleImageUpload($fileKk, 'kk', optional($existingPenghuni)->file_kk);

            $searchKey = $existingPenghuni ? ['id' => $existingPenghuni->id] : ['penghuni_id' => $data['id_penghuni']];

            $nik = !empty($data['nik']) ? $data['nik'] : null;

            DataPenghuni::updateOrCreate(
                $searchKey,
                [
                    'penghuni_id' => $data['id_penghuni'],
                    'nik'        => $nik,
                    'nama'       => $data['nama'],
                    'no_wa'      => $data['no_wa'],
                    'agama'      => $data['agama'],
                    'file_ktp'   => $pathKtp,
                    'file_kk'    => $pathKk,
                ]
            );

            RiwayatMutasi::create([
                'kos_id'         => $data['id_kos'],
                'penghuni_id'    => $data['id_penghuni'],
                'jenis_mutasi'   => $data['jenis_mutasi'],
                'tanggal_mutasi' => $data['tanggal_mutasi'],
            ]);

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function handleImageUpload($file, string $tipeDokumen, ?string $pathLama)
    {
        if (!$file) {
            return $pathLama;
        }

        try {
            if ($pathLama && Storage::disk('public')->exists($pathLama)) {
                Storage::disk('public')->delete($pathLama);
            }

            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getPathname());
            $image->scaleDown(width: 800);
            $encodedImage = $image->toJpeg(quality: 75);

            $namaFileBaru = $tipeDokumen . '_' . Str::random(10) . '.jpg';
            $pathSimpan = 'dokumen_penghuni/' . $namaFileBaru;

            Storage::disk('public')->put($pathSimpan, (string) $encodedImage);

            return $pathSimpan;
        } catch (\Exception $e) {
            return $pathLama;
        }
    }

    public function syncPendapatan(array $data)
    {
        $periodeTagihanFormat = null;
        if (!empty($data['periode_tagihan'])) {
            $periodeTagihanFormat = Carbon::parse($data['periode_tagihan'])->format('Y-m-d');
        }

        return RekapPendapatan::create([
            'kos_id'             => $data['id_kos'],
            'pemilik_id'         => $data['id_pemilik'],
            'nama_kos'           => $data['nama_kos'],
            'nama_penghuni'      => $data['nama_penghuni'],
            'nomor_kamar'        => $data['nomor_kamar'],
            'tipe_kamar'         => $data['tipe_kamar'],
            'periode_tagihan'    => $periodeTagihanFormat,
            'metode_pembayaran'  => $data['metode_pembayaran'],
            'nominal'            => $data['nominal'],
            'tanggal_pembayaran' => $data['tanggal_pembayaran'],
        ]);
    }

    public function syncPenghuni(array $data, $fileKtp, $fileKk)
    {
        DB::beginTransaction();
        try {
            $existingPenghuni = DataPenghuni::where('penghuni_id', $data['id_penghuni'])->first();

            if (!$existingPenghuni && !empty($data['nik'])) {
                $existingPenghuni = DataPenghuni::where('nik', $data['nik'])->first();
            }

            $pathKtp = $this->handleImageUpload($fileKtp, 'ktp', optional($existingPenghuni)->file_ktp);
            $pathKk = $this->handleImageUpload($fileKk, 'kk', optional($existingPenghuni)->file_kk);

            $searchKey = $existingPenghuni ? ['id' => $existingPenghuni->id] : ['penghuni_id' => $data['id_penghuni']];

            $nik = !empty($data['nik']) ? $data['nik'] : null;

            DataPenghuni::updateOrCreate(
                $searchKey,
                [
                    'penghuni_id' => $data['id_penghuni'],
                    'nik'        => $nik,
                    'nama'       => $data['nama'],
                    'no_wa'      => $data['no_wa'],
                    'agama'      => $data['agama'],
                    'file_ktp'   => $pathKtp,
                    'file_kk'    => $pathKk,
                ]
            );

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
