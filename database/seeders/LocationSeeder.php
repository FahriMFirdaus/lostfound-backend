<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['nama_tempat' => 'Gedung Rektorat', 'detail_lokasi' => 'Area lobi lantai 1'],
            ['nama_tempat' => 'Gedung F', 'detail_lokasi' => 'Area kelas dan selasar'],
            ['nama_tempat' => 'Masjid Kampus', 'detail_lokasi' => 'Tempat wudhu atau rak sepatu'],
            ['nama_tempat' => 'Area Parkir Utama', 'detail_lokasi' => 'Parkiran motor mahasiswa'],
            ['nama_tempat' => 'Kantin Kampus', 'detail_lokasi' => 'Area meja makan'],
        ];

        foreach ($locations as $loc) {
            \App\Models\Location::updateOrCreate(['nama_tempat' => $loc['nama_tempat']], $loc);
        }
    }
}
