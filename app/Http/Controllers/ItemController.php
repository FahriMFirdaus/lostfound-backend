<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    // GET /api/v1/items (Mading Digital)
    public function index(Request $request)
    {
        $query = Item::with(['category', 'location', 'user'])
                     ->where('visibility', 'public')
                     ->where('status', 'active');
                     
        if ($request->has('post_type')) {
            $query->where('post_type', $request->post_type);
        }
        
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        if ($request->has('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('judul', 'like', '%' . $request->q . '%')
                  ->orWhere('deskripsi', 'like', '%' . $request->q . '%');
            });
        }

        $items = $query->latest('tanggal_lapor')->paginate(10);
        
        // Privacy Shield: Menghapus kontak hp user dari API Publik
        $items->getCollection()->transform(function ($item) {
            if ($item->user) {
                unset($item->user->no_hp);
            }
            return $item;
        });

        return response()->json(['success' => true, 'data' => $items]);
    }

    // POST /api/v1/items (Laporan Baru)
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:150',
            'deskripsi' => 'required|string',
            'deskripsi_rahasia' => 'required|string',
            'post_type' => 'required|in:lost,found',
            'tanggal_lapor' => 'required|date',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('items', 'public');
        }

        // Rule: Jika found (barang temu), otomatis jadi private (draft)
        $visibility = ($request->post_type == 'found') ? 'private' : 'public';

        $item = Item::create([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'deskripsi_rahasia' => $request->deskripsi_rahasia,
            'foto' => $path,
            'post_type' => $request->post_type,
            'status' => 'active',
            'visibility' => $visibility,
            'tanggal_lapor' => $request->tanggal_lapor,
            'category_id' => $request->category_id,
            'location_id' => $request->location_id,
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil dibuat.',
            'data' => $item
        ], 201);
    }
    
    // PATCH /api/v1/items/{id}/release (Gatekeeper khusus Satpam)
    public function release(Request $request, $id)
    {
        if ($request->user()->role !== 'satpam') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak. Hanya satpam yang dapat melakukan rilis fisik.'], 403);
        }

        $item = Item::findOrFail($id);
        $item->visibility = 'public';
        $item->save();

        return response()->json([
            'success' => true,
            'message' => 'Barang fisik divalidasi. Laporan dipublikasikan.',
            'data' => $item
        ]);
    }
}
