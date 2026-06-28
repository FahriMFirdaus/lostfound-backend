<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Gatekeeper
        if ($request->user() && $request->user()->role !== 'satpam') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $query = User::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        if ($request->has('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate(10);
        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    public function store(Request $request)
    {
        if ($request->user() && $request->user()->role !== 'satpam') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'no_hp' => 'required|string|max:20',
            'role' => 'required|in:mahasiswa,satpam',
            'password' => 'required|string|min:6',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil ditambahkan',
            'data' => $user
        ], 201);
    }

    public function update(Request $request, $id)
    {
        if ($request->user() && $request->user()->role !== 'satpam') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $user = User::find($id);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        $validated = $request->validate([
            'nama_lengkap' => 'sometimes|required|string|max:255',
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'no_hp' => 'sometimes|required|string|max:20',
            'role' => 'sometimes|required|in:mahasiswa,satpam',
            'password' => 'nullable|string|min:6',
        ]);

        if (isset($validated['password']) && $validated['password']) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil diperbarui',
            'data' => $user
        ]);
    }

    public function destroy(Request $request, $id)
    {
        if ($request->user() && $request->user()->role !== 'satpam') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $user = User::find($id);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        if ($request->user() && $user->id === $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Tidak bisa menghapus akun Anda sendiri!'], 400);
        }

        $user->delete();
        return response()->json(['success' => true, 'message' => 'User deleted successfully']);
    }
}
