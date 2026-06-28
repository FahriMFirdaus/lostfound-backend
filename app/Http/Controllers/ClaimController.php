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
            'foto_ktp' => 'required|image|mimes:jpg,jpeg,png|max:5120',
            'foto_bukti_pendukung' => 'required|image|mimes:jpg,jpeg,png|max:5120',
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

        $fotoKtp = null;
        if ($request->hasFile('foto_ktp')) {
            $fotoKtp = $request->file('foto_ktp')->store('claims/ktp', 'public');
        }

        $fotoBukti = null;
        if ($request->hasFile('foto_bukti_pendukung')) {
            $fotoBukti = $request->file('foto_bukti_pendukung')->store('claims/proofs', 'public');
        }

        $claim = Claim::create([
            'item_id' => $request->item_id,
            'user_id' => $request->user()->id,
            'bukti_teks' => $request->bukti_teks,
            'foto_ktp' => $fotoKtp,
            'foto_bukti_pendukung' => $fotoBukti,
            'status_verif' => 'pending',
            'tanggal_klaim' => now()
        ]);

        $claim->activityLogs()->create([
            'action_type' => 'created',
            'description' => 'Mengajukan klaim verifikasi kepemilikan',
            'user_id' => $request->user()->id,
        ]);

        return response()->json(['success' => true, 'message' => 'Klaim berhasil diajukan dan menunggu evaluasi Satpam.', 'data' => $claim], 201);
    }

    // GET /api/v1/claims/{id} (Detail Peninjauan Berkas)
    public function show($id)
    {
        $claim = Claim::with(['item.location', 'item.category', 'user', 'handover', 'activityLogs', 'item.activityLogs'])->findOrFail($id);
        return response()->json(['success' => true, 'data' => $claim]);
    }

    // GET /api/v1/claims/token/{token} (Lihat Klaim Berdasarkan Token)
    public function getByToken($token)
    {
        $claim = Claim::with(['item.location', 'item.category', 'user'])->where('token_pengambilan', $token)->firstOrFail();
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

        $logDesc = 'Status klaim diubah menjadi: ' . $request->status_verif;
        if ($request->status_verif === 'approved') {
            $logDesc = 'Klaim disetujui. Silakan datang ke pos satpam untuk mengambil barang.';
        } elseif ($request->status_verif === 'clarification_required') {
            $logDesc = 'Klaim butuh klarifikasi: ' . $request->catatan_evaluasi;
        }

        $claim->activityLogs()->create([
            'action_type' => 'status_changed',
            'description' => $logDesc,
            'user_id' => $request->user()->id,
            'metadata' => [
                'status' => $request->status_verif
            ]
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Status verifikasi klaim berhasil diperbarui.',
            'data' => $claim
        ]);
    }
}
