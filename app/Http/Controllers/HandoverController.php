<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\Handover;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class HandoverController extends Controller
{
    // POST /api/v1/handovers (Proses Serah Terima Fisik)
    public function store(Request $request)
    {
        // Gatekeeper: Hanya satpam yang bisa melakukan pemrosesan serah terima
        if ($request->user()->role !== 'satpam') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak. Hanya satpam yang berhak mengakses fitur serah terima.'], 403);
        }

        $request->validate([
            'token_pengambilan' => 'required|string|exists:claims,token_pengambilan',
            'foto_materai' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        // Verifikasi token 12-digit (LF-ORANGE-XXXXXX)
        $claim = Claim::where('token_pengambilan', $request->token_pengambilan)
                      ->where('status_verif', 'approved')
                      ->firstOrFail();

        // Mencegah eksekusi ganda pada satu token yang sama
        if (Handover::where('claim_id', $claim->id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Token sudah digunakan! Barang ini telah diserahterimakan.'], 400);
        }

        $materaiPath = null;
        if ($request->hasFile('foto_materai')) {
            $materaiPath = $request->file('foto_materai')->store('handovers', 'public');
        }
        $handover = DB::transaction(function () use ($claim, $request, $materaiPath) {
            $handover = Handover::create([
                'claim_id' => $claim->id,
                'admin_id' => $request->user()->id,
                'foto_materai' => $materaiPath,
                'tanggal_serah_terima' => now(),
            ]);

            // Archiving: Mengubah status barang dan klaim menjadi 'returned'
            $item = $claim->item;
            $item->status = 'returned';
            $item->save();

            $claim->status_verif = 'returned';
            $claim->save();
            
            return $handover;
        });

        return response()->json([
            'success' => true,
            'message' => 'Proses serah terima berhasil divalidasi. Laporan barang resmi diarsipkan.',
            'data' => $handover
        ], 201);
    }

    // GET /api/v1/handovers/{id} (Lihat Arsip Laporan & Bukti)
    public function show(Request $request, $id)
    {
        $handover = Handover::with(['claim.item', 'claim.user', 'admin'])->findOrFail($id);
        
        // Privacy Shield: Hanya pemilik barang (mahasiswa terkait) atau satpam yang boleh melihat arsip ini
        if ($request->user()->role !== 'satpam' && $handover->claim->user_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak. Berkas arsip bersifat rahasia.'], 403);
        }

        return response()->json(['success' => true, 'data' => $handover]);
    }
}
