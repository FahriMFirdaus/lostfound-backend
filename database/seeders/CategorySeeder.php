<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['nama_kategori' => 'Elektronik', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Dokumen (KTM/KTP)', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Kunci', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Pakaian / Aksesoris', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Lain-lain', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('categories')->insert($categories);
    }
}
