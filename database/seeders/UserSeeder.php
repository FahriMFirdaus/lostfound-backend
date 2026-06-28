<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Insert or update Satpam
        \App\Models\User::updateOrCreate(
            ['email' => 'satpam@unper.ac.id'],
            [
                'nama_lengkap' => 'Admin Satpam Pos Utama',
                'password' => Hash::make('password123'),
                'no_hp' => '081111111111',
                'role' => 'satpam',
            ]
        );
        
        // Insert Contoh Mahasiswa dihapus sesuai permintaan
    }
}
