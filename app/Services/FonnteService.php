<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    public function sendMessage(string $target, string $message): bool
    {
        $token = env('FONNTE_TOKEN');

        if (!$token) {
            Log::error('Fonnte Token is missing in .env file.');
            return false;
        }

        $target = $this->formatNumber($target);

        try {
            $response = Http::withHeaders([
                'Authorization' => $token
            ])->post('https://api.fonnte.com/send', [
                'target' => $target,
                'message' => $message,
                'countryCode' => '62',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['status']) && $data['status'] == true) {
                    return true;
                }
                
                Log::error('Fonnte API returned a failure status:', $data ?? []);
                return false;
            }

            Log::error('Fonnte HTTP request failed. Status: ' . $response->status());
            return false;

        } catch (\Exception $e) {
            Log::error('Exception during Fonnte sending: ' . $e->getMessage());
            return false;
        }
    }

    private function formatNumber(string $number): string
    {
        $number = preg_replace('/[\s\-\+]+/', '', $number);

        if (strpos($number, '0') === 0) {
            $number = '62' . substr($number, 1);
        }

        return $number;
    }
}
