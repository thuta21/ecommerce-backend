<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::whereNull('parent_id')->with('children')->get();
        return response()->json(['data' => $categories]);
    }

    public function show(Category $category): JsonResponse
    {
        return response()->json($category->load('products'));
    }
}
