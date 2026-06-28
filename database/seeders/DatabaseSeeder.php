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
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            LocationSeeder::class,
        ]);

        $satpam = User::where('role', 'satpam')->first();
        $mahasiswa1 = User::where('role', 'student')->first();
        $mahasiswa2 = User::where('email', 'ani@student.unper.ac.id')->first() ?? User::where('role', 'student')->skip(1)->first() ?? $mahasiswa1;
        
        $kategories = Category::all()->all();
        $lokasis = Location::all()->all();

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
