<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CartStoreRequest;
use App\Http\Requests\CartUpdateRequest;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index(): JsonResponse
    {
        $cartItems = Cart::with('product')->where('user_id', Auth::id())->get();
        return response()->json(['data' => $cartItems]);
    }

    public function store(CartStoreRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $product = Product::findOrFail($validatedData['product_id']);

        if ($product->stock_quantity < $validatedData['quantity']) {
            return response()->json(['message' => 'Not enough stock for the requested quantity.'], 400);
        }

        $cartItem = Cart::updateOrCreate(
            ['user_id' => Auth::id(), 'product_id' => $validatedData['product_id']],
            ['quantity' => $validatedData['quantity']]
        );

        return response()->json($cartItem->load('product'), 201);
    }

    public function update(CartUpdateRequest $request, Cart $cart): JsonResponse
    {
        if ($cart->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validatedData = $request->validated();

        if ($cart->product->stock_quantity < $validatedData['quantity']) {
            return response()->json(['message' => 'Not enough stock for the requested quantity.'], 400);
        }

        $cart->update(['quantity' => $validatedData['quantity']]);

        return response()->json($cart->load('product'));
    }

    public function destroy(Cart $cart): JsonResponse
    {
        if ($cart->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $cart->delete();

        return response()->json(['message' => 'Item removed from cart']);
    }
}
