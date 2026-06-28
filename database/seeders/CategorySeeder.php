<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['nama_kategori' => 'Elektronik'],
            ['nama_kategori' => 'Dokumen (KTM/KTP)'],
            ['nama_kategori' => 'Kunci'],
            ['nama_kategori' => 'Pakaian / Aksesoris'],
            ['nama_kategori' => 'Lain-lain'],
        ];

        foreach ($categories as $cat) {
            \App\Models\Category::updateOrCreate(['nama_kategori' => $cat['nama_kategori']], $cat);
        }
    }
}
