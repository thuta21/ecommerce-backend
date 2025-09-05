<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::query()->with('category');
        $perPage = min(max($request->integer('per_page', 10), 1), 100);
        $page = max($request->integer('page', 1), 1);
        $search = trim((string) $request->get('search', $request->get('q', '')));
        $direction = strtolower((string) $request->get('sort')) === 'desc' ? 'desc' : 'asc';

        $query->when($search, function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%");
        })->when($request->has('category'), function ($q) use ($request) {
            $q->whereHas('category', function ($q) use ($request) {
                $q->where('id', $request->category);
            });
        })->when($request->has('sort'), function ($q) use ($direction) {
            $q->orderBy('price', $direction);
        });

        $products = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json($products);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json(['data' => $product->load('category')]);
    }
}
