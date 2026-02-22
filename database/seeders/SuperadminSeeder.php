<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\User;

class SuperadminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superadminRole = Role::where('name', 'superadmin')->first();
        if ($superadminRole) {
            User::firstOrCreate(
                ['email' => 'superadmin@sipenkos.com'],
                [
                    'username' => 'superadmin',
                    'password' => Hash::make('superadmin'),
                    'role_id' => $superadminRole->id,
                ]
            );
        }
    }
}
