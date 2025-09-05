<?php

use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::controller(AuthenticationController::class)->group(function () {
        Route::get('/user', 'user');
        Route::post('/logout', 'logout');
    });

    Route::controller(CartController::class)->group(function () {
        Route::get('/cart', 'index');
        Route::post('/cart', 'store');
        Route::put('/cart/{cart}', 'update');
        Route::delete('/cart/{cart}', 'destroy');
    });

    Route::controller(OrderController::class)->group(function () {
        Route::get('/orders', 'index');
        Route::post('/orders', 'store'); // Checkout
        Route::get('/orders/{order}', 'show');
    });
});

Route::controller(AuthenticationController::class)->group(function () {
    Route::post('/login', 'login')->name('login');
    Route::post('/register', 'register')->name('register');
});

Route::controller(ProductController::class)->group(function () {
    Route::get('/products', 'index');
    Route::get('/products/{product}', 'show');
});

Route::controller(CategoryController::class)->group(function () {
    Route::get('/categories', 'index');
    Route::get('/categories/{category}', 'show');
});
