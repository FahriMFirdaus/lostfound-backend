<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['nama_tempat' => 'Gedung Rektorat', 'detail_lokasi' => 'Area lobi lantai 1', 'created_at' => now(), 'updated_at' => now()],
            ['nama_tempat' => 'Gedung F', 'detail_lokasi' => 'Area kelas dan selasar', 'created_at' => now(), 'updated_at' => now()],
            ['nama_tempat' => 'Masjid Kampus', 'detail_lokasi' => 'Tempat wudhu atau rak sepatu', 'created_at' => now(), 'updated_at' => now()],
            ['nama_tempat' => 'Area Parkir Utama', 'detail_lokasi' => 'Parkiran motor mahasiswa', 'created_at' => now(), 'updated_at' => now()],
            ['nama_tempat' => 'Kantin Kampus', 'detail_lokasi' => 'Area meja makan', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('locations')->insert($locations);
    }
}
