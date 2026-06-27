<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;

class LocationController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => Location::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(['nama_tempat' => 'required|string|max:100']);
        $loc = Location::create(['nama_tempat' => $request->nama_tempat]);
        return response()->json(['success' => true, 'data' => $loc]);
    }

    public function destroy($id)
    {
        $loc = Location::find($id);
        if($loc) $loc->delete();
        return response()->json(['success' => true]);
    }
}
