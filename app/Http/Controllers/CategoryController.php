<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    /**
     * Get a list of all categories.
     *
     * @return JsonResponse
     */
    public function getCategories(): JsonResponse
    {
        $categories = Category::orderBy('name')->get();

        return response()->json($categories);
    }
}
