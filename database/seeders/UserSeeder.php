<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Insert Satpam
        DB::table('users')->insert([
            'nama_lengkap' => 'Admin Satpam Pos Utama',
            'email' => 'satpam@unper.ac.id',
            'password' => Hash::make('password123'),
            'no_hp' => '081111111111',
            'role' => 'satpam',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Insert Contoh Mahasiswa
        DB::table('users')->insert([
            'nama_lengkap' => 'Ahmad Dahlan',
            'email' => 'ahmad@student.unper.ac.id',
            'password' => Hash::make('password123'),
            'no_hp' => '082222222222',
            'role' => 'student',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
