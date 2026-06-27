<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClaimController extends Controller
{
    // GET /api/v1/claims (Antrean Satpam)
    public function index(Request $request)
    {
        if ($request->user()->role !== 'satpam') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $claims = Claim::with(['item', 'user'])->latest('tanggal_klaim')->get();
        return response()->json(['success' => true, 'data' => $claims]);
    }

    // GET /api/v1/claims/my-claims (Daftar Klaim Milik Sendiri)
    public function myClaims(Request $request)
    {
        $claims = Claim::with(['item'])->where('user_id', $request->user()->id)->latest('tanggal_klaim')->get();
        return response()->json(['success' => true, 'data' => $claims]);
    }

    // POST /api/v1/claims (Ajukan Klaim Baru)
    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'bukti_teks' => 'required|string',
            'bukti_foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $item = Item::findOrFail($request->item_id);

        // Pencegahan Self-Claim
        if ($item->user_id === $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Anda tidak bisa mengklaim barang temuan Anda sendiri.'], 400);
        }

        // Anti-Spam Shield: Cek maksimal 2 klaim pending/active per user
        $activeClaimsCount = Claim::where('user_id', $request->user()->id)
                                  ->where('status_verif', 'pending')
                                  ->count();
        if ($activeClaimsCount >= 2) {
            return response()->json(['success' => false, 'message' => 'Anti-Spam Shield Aktif: Anda sudah mencapai batas maksimal 2 klaim yang sedang diproses.'], 429);
        }

        $path = null;
        if ($request->hasFile('bukti_foto')) {
            $path = $request->file('bukti_foto')->store('claims', 'public');
        }

        $claim = Claim::create([
            'item_id' => $request->item_id,
            'user_id' => $request->user()->id,
            'bukti_teks' => $request->bukti_teks,
            'bukti_foto' => $path,
            'status_verif' => 'pending',
            'tanggal_klaim' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Klaim berhasil diajukan dan menunggu evaluasi Satpam.', 'data' => $claim], 201);
    }

    // GET /api/v1/claims/{id} (Detail Peninjauan Berkas)
    public function show($id)
    {
        $claim = Claim::with(['item', 'user'])->findOrFail($id);
        return response()->json(['success' => true, 'data' => $claim]);
    }

    // PATCH /api/v1/claims/{id}/verify (Keputusan Gatekeeper / Satpam)
    public function verify(Request $request, $id)
    {
        if ($request->user()->role !== 'satpam') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $request->validate([
            'status_verif' => 'required|in:approved,rejected,clarification_required',
            'catatan_evaluasi' => 'nullable|string'
        ]);

        $claim = Claim::findOrFail($id);
        $claim->status_verif = $request->status_verif;
        $claim->catatan_evaluasi = $request->catatan_evaluasi;

        // Otomatis cetak Takeout Token jika disetujui (Format: LF-ORANGE-XXXXXX)
        if ($request->status_verif === 'approved' && empty($claim->token_pengambilan)) {
            $randomString = strtoupper(Str::random(6)); // 6 Karakter Acak (Alfanumerik)
            $claim->token_pengambilan = 'LF-ORANGE-' . $randomString;
        }

        $claim->save();

        return response()->json([
            'success' => true, 
            'message' => 'Status verifikasi klaim berhasil diperbarui.',
            'data' => $claim
        ]);
    }
}
