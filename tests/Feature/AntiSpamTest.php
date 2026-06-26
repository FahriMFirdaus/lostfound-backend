<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Location;
use App\Models\Claim;

class AntiSpamTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_create_more_than_two_pending_claims()
    {
        // Setup data dummy
        $user = User::factory()->create(['role' => 'student']);
        $category = Category::create(['nama_kategori' => 'Elektronik']);
        $location = Location::create(['nama_tempat' => 'Gedung A']);
        
        $item1 = Item::create([
            'judul' => 'HP Hilang', 'deskripsi' => '-', 'deskripsi_rahasia' => '-',
            'post_type' => 'found', 'status' => 'active', 'visibility' => 'public',
            'tanggal_lapor' => now(), 'category_id' => $category->id, 'location_id' => $location->id,
            'user_id' => User::factory()->create()->id
        ]);
        
        $item2 = Item::create([
            'judul' => 'Dompet', 'deskripsi' => '-', 'deskripsi_rahasia' => '-',
            'post_type' => 'found', 'status' => 'active', 'visibility' => 'public',
            'tanggal_lapor' => now(), 'category_id' => $category->id, 'location_id' => $location->id,
            'user_id' => User::factory()->create()->id
        ]);
        
        $item3 = Item::create([
            'judul' => 'Kunci', 'deskripsi' => '-', 'deskripsi_rahasia' => '-',
            'post_type' => 'found', 'status' => 'active', 'visibility' => 'public',
            'tanggal_lapor' => now(), 'category_id' => $category->id, 'location_id' => $location->id,
            'user_id' => User::factory()->create()->id
        ]);

        // Beri 2 klaim pending ke user
        Claim::create(['item_id' => $item1->id, 'user_id' => $user->id, 'bukti_teks' => 'Ini hp saya', 'status_verif' => 'pending']);
        Claim::create(['item_id' => $item2->id, 'user_id' => $user->id, 'bukti_teks' => 'Ini dompet saya', 'status_verif' => 'pending']);

        // Test ajukan klaim ke-3
        $response = $this->actingAs($user)->postJson('/api/v1/claims', [
            'item_id' => $item3->id,
            'bukti_teks' => 'Ini kunci saya'
        ]);

        // Harus ditolak (429 Too Many Requests)
        $response->assertStatus(429)
                 ->assertJsonFragment(['success' => false]);
    }
}
