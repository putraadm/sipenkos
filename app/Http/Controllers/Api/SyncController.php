<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DataKos;
use App\Models\DataPenghuni;
use App\Models\RekapPendapatan;
use App\Models\RiwayatMutasi;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class SyncController extends Controller
{
    use ApiResponse;
    public function syncKos(Request $request)
    {
        $validated = $request->validate([
            'id_kos'     => 'required|integer',
            'id_pemilik' => 'required|integer',
            'nama_pemilik' => 'required|string',
            'no_wa_pemilik' => 'nullable|string',
            'nama_kos'   => 'required|string',
            'alamat'     => 'required|string',
        ]);

        try {
            DataKos::updateOrCreate(
                ['kos_id' => $validated['id_kos']],
                [
                    'pemilik_id' => $validated['id_pemilik'],
                    'nama_pemilik' => $validated['nama_pemilik'],
                    'no_wa_pemilik' => $validated['no_wa_pemilik'],
                    'nama_kos'   => $validated['nama_kos'],
                    'alamat'     => $validated['alamat'],
                ]
            );

            return $this->successResponse(
                $validated,
                'Data Kos berhasil disinkronisasi.',
                200
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal menyinkronkan data kos: ' . $e->getMessage(), 500);
        }
    }

    public function syncMutasi(Request $request)
    {
        $validated = $request->validate([
            'id_penghuni'    => 'required|integer',
            'nama'           => 'required|string',
            'no_wa'          => 'nullable|string',
            'agama'          => 'nullable|string',
            'file_ktp'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'file_kk'        => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'id_kos'         => 'required|integer',
            'jenis_mutasi'   => 'required|in:masuk,keluar',
            'tanggal_mutasi' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $existingPenghuni = DataPenghuni::where('penghuni_id', $validated['id_penghuni'])->first();

            $pathKtp = $this->handleImageUpload(
                $request->file('file_ktp'),
                'ktp',
                optional($existingPenghuni)->file_ktp
            );

            $pathKk = $this->handleImageUpload(
                $request->file('file_kk'),
                'kk',
                optional($existingPenghuni)->file_kk
            );

            DataPenghuni::updateOrCreate(
                ['penghuni_id' => $validated['id_penghuni']],
                [
                    'nama'       => $validated['nama'],
                    'no_wa'      => $validated['no_wa'],
                    'agama'      => $validated['agama'],
                    'file_ktp'   => $pathKtp,
                    'file_kk'    => $pathKk,
                ]
            );

            RiwayatMutasi::create([
                'kos_id'         => $validated['id_kos'],
                'penghuni_id'    => $validated['id_penghuni'],
                'jenis_mutasi'   => $validated['jenis_mutasi'],
                'tanggal_mutasi' => $validated['tanggal_mutasi'],
            ]);

            DB::commit();

            return $this->successResponse(
                ['id_penghuni' => $validated['id_penghuni'], 'jenis_mutasi' => $validated['jenis_mutasi']],
                'Data mutasi penghuni berhasil dicatat.'
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Gagal mencatat mutasi: ' . $e->getMessage(), 500);
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

    public function syncPendapatan(Request $request)
    {
        $validated = $request->validate([
            'id_kos'             => 'required|integer',
            'id_pemilik'         => 'required|integer',
            'nama_kos'           => 'required|string',
            'nama_penghuni'      => 'nullable|string',
            'nomor_kamar'        => 'nullable|string',
            'tipe_kamar'         => 'nullable|string',
            'periode_tagihan'    => 'nullable|date',
            'metode_pembayaran'  => 'nullable|string',
            'nominal'            => 'required|numeric',
            'tanggal_pembayaran' => 'required|date_format:Y-m-d H:i:s',
        ]);

        try {
            RekapPendapatan::create([
                'kos_id'             => $validated['id_kos'],
                'pemilik_id'         => $validated['id_pemilik'],
                'nama_kos'           => $validated['nama_kos'],
                'nama_penghuni'      => $validated['nama_penghuni'],
                'nomor_kamar'        => $validated['nomor_kamar'],
                'tipe_kamar'         => $validated['tipe_kamar'],
                'periode_tagihan'    => $validated['periode_tagihan'],
                'metode_pembayaran'  => $validated['metode_pembayaran'],
                'nominal'            => $validated['nominal'],
                'tanggal_pembayaran' => $validated['tanggal_pembayaran'],
            ]);

            return $this->successResponse(null, 'Data Pendapatan berhasil dicatat.');
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal mencatat pendapatan: ' . $e->getMessage(), 500);
        }
    }
}
