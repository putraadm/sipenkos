<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\LaporanPemilikKos;
use App\Services\FonnteService;
use Illuminate\Console\Command;

class SendKosReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-kos-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send WhatsApp reminder to Pemilik Kos to report data';

    /**
     * Execute the console command.
     */
    public function handle(FonnteService $fonnteService)
    {
        $this->info('Starting WhatsApp reminder process...');

        $currentMonth = date('n');
        $currentYear = date('Y');

        $pemiliks = User::whereHas('role', function($q) {
            $q->where('name', 'pemilik_kos');
        })->whereNotNull('no_wa')->get();

        $sentCount = 0;

        foreach ($pemiliks as $pemilik) {
            $hasReported = LaporanPemilikKos::where('user_id', $pemilik->id)
                ->where('periode_bulan', $currentMonth)
                ->where('periode_tahun', $currentYear)
                ->exists();

            if (!$hasReported) {
                $monthName = date('F');
                
                $monthsId = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                $monthStr = $monthsId[$currentMonth - 1];

                $msg = "Halo Bpk/Ibu *{$pemilik->username}*,\n\n";
                $msg .= "Ini adalah pengingat dari sistem SIPENKOS (Sistem Pendataan Kos).\n";
                $msg .= "Mohon untuk segera memperbarui data hunian kos Anda untuk periode *{$monthStr} {$currentYear}*.\n\n";
                $msg .= "Silakan login ke dashboard sistem: " . url('/login') . "\n\n";
                $msg .= "Terima kasih atas kerja samanya.";

                $success = $fonnteService->sendMessage($pemilik->no_wa, $msg);

                if ($success) {
                    $this->info("Sent to {$pemilik->username} ({$pemilik->no_wa})");
                    $sentCount++;
                } else {
                    $this->error("Failed to send to {$pemilik->username} ({$pemilik->no_wa})");
                }
            }
        }

        $this->info("Reminder process completed. Total sent: {$sentCount}");
    }
}
