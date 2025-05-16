<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    /**
     * GET /api/categories
     */
    public function index(): JsonResponse
    {
        return response()->json(Category::all());
    }
}
