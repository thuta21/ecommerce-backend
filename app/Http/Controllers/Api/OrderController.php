<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderStoreRequest;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Psy\Exception\ErrorException;

class OrderController extends Controller
{
    public function index(): JsonResponse
    {
        $orders = Order::with('items.product')->where('user_id', Auth::id())->latest()->get();
        return response()->json(['data' => $orders]);
    }

    public function store(OrderStoreRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $user = Auth::user();
        $cartItems = Cart::with('product')->where('user_id', $user->id)->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Your cart is empty.'], 400);
        }

        return DB::transaction(function () use ($user, $cartItems, $validatedData) {
            $totalAmount = 0;

            foreach ($cartItems as $item) {
                // Check for sufficient stock
                if ($item->product->stock_quantity < $item->quantity) {
                    throw new ErrorException('Not enough stock for product: '.$item->product->name);
                }
                $totalAmount += $item->product->price * $item->quantity;
            }

            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => $totalAmount,
                'shipping_address' => $validatedData['shipping_address'],
                'payment_method' => $validatedData['payment_method'],
                'status' => 'pending',
            ]);

            foreach ($cartItems as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price, // Store price at time of purchase
                ]);

                $item->product->decrement('stock_quantity', $item->quantity);
            }

            Cart::where('user_id', $user->id)->delete();

            return response()->json($order->load('items.product'), 201);
        });
    }

    public function show(Order $order): JsonResponse
    {
        if ($order->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json(['data' => $order->load('items.product')]);
    }
}
