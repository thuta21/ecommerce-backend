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
                'iPhone 16 Pro Max',
                'iPhone 15',
                'iPhone 14',
            ],
            'Accessories' => [
                'AirPods Pro',
                'MagSafe Charger',
                'Apple Watch Series 10',
            ],
            'Macbook' => [
                'MacBook Air M2',
                'MacBook Air M3',
                'MacBook Pro 14-inch',
            ],
        ];

        $imageFiles = [
            'iPhone 16 Pro Max' => 'iphone16-promax.jpg',
            'iPhone 15' => 'iphone15.jpg',
            'iPhone 14' => 'iphone14.jpg',
            'AirPods Pro' => 'airpods-pro.jpg',
            'MagSafe Charger' => 'magsafe-charger.jpg',
            'Apple Watch Series 10' => 'apple-watch-10.jpg',
            'MacBook Air M2' => 'macbook-air-m2.jpg',
            'MacBook Air M3' => 'macbook-air-m3.jpg',
            'MacBook Pro 14-inch' => 'macbook-pro-14.jpg',
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
                    'image_url' => asset('seed_images/' . $imageFiles[$productName]),
                ]);
            }
        }
    }
}
