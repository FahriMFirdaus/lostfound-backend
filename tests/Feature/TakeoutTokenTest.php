<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Location;
use App\Models\Claim;

class TakeoutTokenTest extends TestCase
{
    use RefreshDatabase;

    public function test_token_is_generated_on_claim_approval()
    {
        // Setup data dummy
        $satpam = User::factory()->create(['role' => 'satpam']);
        $student = User::factory()->create(['role' => 'student']);
        $category = Category::create(['nama_kategori' => 'Elektronik']);
        $location = Location::create(['nama_tempat' => 'Gedung A']);
        
        $item = Item::create([
            'judul' => 'HP Hilang', 'deskripsi' => '-', 'deskripsi_rahasia' => '-',
            'post_type' => 'found', 'status' => 'active', 'visibility' => 'public',
            'tanggal_lapor' => now(), 'category_id' => $category->id, 'location_id' => $location->id,
            'user_id' => $student->id
        ]);
        
        $claim = Claim::create([
            'item_id' => $item->id, 'user_id' => $student->id, 'bukti_teks' => 'Ini punya saya', 'status_verif' => 'pending'
        ]);

        // Satpam meng-approve klaim
        $response = $this->actingAs($satpam)->patchJson("/api/v1/claims/{$claim->id}/verify", [
            'status_verif' => 'approved'
        ]);

        $response->assertStatus(200);
        
        $claim->refresh();
        
        // Pastikan token tergenerate dengan format LF-ORANGE-XXXXXX
        $this->assertNotNull($claim->token_pengambilan);
        $this->assertStringStartsWith('LF-ORANGE-', $claim->token_pengambilan);
        $this->assertEquals(16, strlen($claim->token_pengambilan)); // LF-ORANGE- (10) + 6 random
    }
}
