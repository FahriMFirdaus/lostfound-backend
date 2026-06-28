<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => Category::all()
        ]);
    }
}
