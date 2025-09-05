<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $categoriesWithProducts = [
            'iPhone' => [
                'iPhone 17 Pro',
                'iPhone 17',
                'iPhone 16 Pro',
                'iPhone 16',
                'iPhone 16e',
            ],
            'Accessories' => [
                'AirPods Pro',
                'MagSafe Charger',
                'Apple Watch Series 10',
                'Lightning Cable',
                'iPhone 17 Case',
            ],
            'Macbook' => [
                'MacBook Air M2',
                'MacBook Air M3',
                'MacBook Pro 14-inch',
                'MacBook Pro 16-inch',
                'Mac Studio Display',
            ],
        ];

        foreach ($categoriesWithProducts as $categoryName => $products) {
            // Create category
            $category = Category::create([
                'name' => $categoryName,
                'slug' => Str::slug($categoryName),
            ]);

            // Create products under this category
            foreach ($products as $productName) {
                Product::create([
                    'category_id' => $category->id,
                    'name' => $productName,
                    'slug' => Str::slug($productName).'-'.Str::random(5), // ensure unique
                    'description' => 'This is a description for '.$productName,
                    'price' => fake()->randomFloat(2, 200, 3000),
                    'stock_quantity' => fake()->numberBetween(10, 100),
                    'image_url' => fake()->imageUrl(640, 480, 'products', true),
                ]);
            }
        }
    }
}
