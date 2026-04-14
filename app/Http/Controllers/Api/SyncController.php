<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SyncService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class SyncController extends Controller
{
    use ApiResponse;

    protected $syncService;

    public function __construct(SyncService $syncService)
    {
        $this->syncService = $syncService;
    }

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
            $this->syncService->syncKos($validated);
            return $this->successResponse($validated, 'Data Kos berhasil disinkronisasi.', 200);
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal menyinkronkan data kos: ' . $e->getMessage(), 500);
        }
    }

    public function syncMutasi(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('Incoming Sync Mutasi:', $request->except(['file_ktp', 'file_kk']));

        $validated = $request->validate([
            'id_penghuni'    => 'required|integer',
            'nik'            => 'nullable|string',
            'nama'           => 'required|string',
            'no_wa'          => 'nullable|string',
            'agama'          => 'nullable|string',
            'file_ktp'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'file_kk'        => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'id_kos'         => 'required|integer',
            'jenis_mutasi'   => 'required|in:masuk,keluar',
            'tanggal_mutasi' => 'required|date',
        ]);

        try {
            $this->syncService->syncMutasi($validated, $request->file('file_ktp'), $request->file('file_kk'));
            return $this->successResponse(
                ['id_penghuni' => $validated['id_penghuni'], 'jenis_mutasi' => $validated['jenis_mutasi']],
                'Data mutasi penghuni berhasil dicatat.',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal mencatat mutasi: ' . $e->getMessage(), 500);
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
            'periode_tagihan'    => 'nullable|string',
            'metode_pembayaran'  => 'nullable|string',
            'nominal'            => 'required|numeric',
            'tanggal_pembayaran' => 'required|date_format:Y-m-d H:i:s',
        ]);

        try {
            $this->syncService->syncPendapatan($validated);
            return $this->successResponse(null, 'Data Pendapatan berhasil dicatat.', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal mencatat pendapatan: ' . $e->getMessage(), 500);
        }
    }

    public function syncPenghuni(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('Incoming Sync Penghuni:', $request->except(['file_ktp', 'file_kk']));

        $validated = $request->validate([
            'id_penghuni'    => 'required|integer',
            'nik'            => 'nullable|string',
            'nama'           => 'required|string',
            'no_wa'          => 'nullable|string',
            'agama'          => 'nullable|string',
            'file_ktp'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'file_kk'        => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        try {
            $this->syncService->syncPenghuni($validated, $request->file('file_ktp'), $request->file('file_kk'));
            return $this->successResponse(
                ['id_penghuni' => $validated['id_penghuni']],
                'Data profil penghuni berhasil diperbarui secara tersinkronisasi.',
                200
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal memperbarui data profil penghuni: ' . $e->getMessage(), 500);
        }
    }
}
