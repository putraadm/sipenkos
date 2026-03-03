<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AppClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clientRole = Role::where('name', 'sistem')->first();
        if ($clientRole) {
            $clientUser = User::firstOrCreate(
                ['email' => 'sistem@sipenkos.com'],
                [
                    'username' => 'sistem_sipenkos',
                    'password' => Hash::make('password_rahasia_sipenkos'),
                    'role_id'  => $clientRole->id,
                ]
            );
            $clientUser->tokens()->delete();

            $token = $clientUser->createToken('App1-Integration-Token')->plainTextToken;

            $this->command->info('User App 1 berhasil dibuat!');
            $this->command->warn('SIMPAN TOKEN INI BAIK-BAIK, HANYA MUNCUL SEKALI:');
            $this->command->info($token);
        } else {
            $this->command->error('Role "sistem" belum ada. Jalankan RoleSeeder terlebih dahulu.');
        }
    }
}
