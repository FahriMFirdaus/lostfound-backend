<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Location;
use App\Models\Item;
use App\Models\Claim;
use App\Models\Handover;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Buat Akun Satpam
        $satpam = User::firstOrCreate(['email' => 'satpam@unper.ac.id'], [
            'nama_lengkap' => 'Komandan Satpam',
            'password' => Hash::make('password'),
            'no_hp' => '08111111111',
            'role' => 'satpam'
        ]);

        // Buat Akun Mahasiswa
        $mahasiswa1 = User::firstOrCreate(['email' => 'budi@student.unper.ac.id'], [
            'nama_lengkap' => 'Budi Santoso',
            'password' => Hash::make('password'),
            'no_hp' => '08222222222',
            'role' => 'student'
        ]);

        $mahasiswa2 = User::firstOrCreate(['email' => 'ani@student.unper.ac.id'], [
            'nama_lengkap' => 'Ani Lestari',
            'password' => Hash::make('password'),
            'no_hp' => '08333333333',
            'role' => 'student'
        ]);

        // Buat Kategori
        $kategories = [
            Category::firstOrCreate(['nama_kategori' => 'Elektronik']),
            Category::firstOrCreate(['nama_kategori' => 'Aksesoris']),
            Category::firstOrCreate(['nama_kategori' => 'Dokumen Pribadi']),
            Category::firstOrCreate(['nama_kategori' => 'Pakaian']),
            Category::firstOrCreate(['nama_kategori' => 'Lainnya']),
        ];

        // Buat Lokasi
        $lokasis = [
            Location::firstOrCreate(['nama_tempat' => 'Gedung Rektorat']),
            Location::firstOrCreate(['nama_tempat' => 'Gedung Fakultas']),
            Location::firstOrCreate(['nama_tempat' => 'Perpustakaan']),
            Location::firstOrCreate(['nama_tempat' => 'Kantin']),
            Location::firstOrCreate(['nama_tempat' => 'Parkiran']),
        ];

        $faker = \Faker\Factory::create('id_ID');

        // Buat Barang Dummy (Found & Lost)
        $items = [];
        for ($i = 1; $i <= 20; $i++) {
            $type = $i % 2 == 0 ? 'found' : 'lost';
            $status = $type == 'found' ? ($i % 3 == 0 ? 'returned' : 'active') : 'active';

            $items[] = Item::create([
                'judul' => ucfirst($faker->word) . ' ' . $kategories[array_rand($kategories)]->nama_kategori,
                'deskripsi' => $faker->sentence(10),
                'deskripsi_rahasia' => $faker->sentence(5),
                'foto' => null,
                'post_type' => $type,
                'tanggal_lapor' => Carbon::now()->subDays(rand(0, 10)),
                'status' => $status,
                'visibility' => 'public', // SET PUBLIC SO THEY SHOW IN MADING
                'user_id' => $i % 2 == 0 ? $mahasiswa2->id : $mahasiswa1->id,
                'category_id' => $kategories[array_rand($kategories)]->id,
                'location_id' => $lokasis[array_rand($lokasis)]->id,
            ]);
        }

        // Buat Klaim Dummy untuk barang 'found'
        foreach ($items as $item) {
            if ($item->post_type === 'found' && $item->status === 'returned') {
                $claim = Claim::create([
                    'item_id' => $item->id,
                    'user_id' => $mahasiswa1->id,
                    'tanggal_klaim' => Carbon::now()->subDays(rand(0, 2)),
                    'bukti_teks' => 'Saya punya foto bareng barang ini.',
                    'status_verif' => 'approved',
                ]);
                
                Handover::create([
                    'claim_id' => $claim->id,
                    'admin_id' => $satpam->id,
                    'foto_serah_terima' => 'dummy_handover.jpg',
                    'tanggal_serah_terima' => Carbon::now(),
                ]);
            } elseif ($item->post_type === 'found' && $item->id % 4 == 0) {
                // Buat 1-2 antrean verifikasi
                $claim = Claim::create([
                    'item_id' => $item->id,
                    'user_id' => $mahasiswa1->id,
                    'tanggal_klaim' => Carbon::now(),
                    'bukti_teks' => 'Ini barang saya.',
                    'status_verif' => 'pending',
                ]);
            }
        }
    }
}
