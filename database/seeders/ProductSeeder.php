<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Electronics - Smartphones
            [
                'name' => 'iPhone 15 Pro',
                'slug' => 'iphone-15-pro',
                'short_description' => 'Latest iPhone with advanced features',
                'description' => 'The iPhone 15 Pro features a titanium design, A17 Pro chip, and advanced camera system.',
                'sku' => 'IPH15PRO001',
                'price' => 999.00,
                'compare_price' => 1099.00,
                'cost_price' => 750.00,
                'status' => 'active',
                'visibility' => 'visible',
                'weight' => 0.187,
                'is_featured' => true,
                'categories' => ['smartphones', 'electronics'],
                'primary_category' => 'smartphones'
            ],
            [
                'name' => 'Samsung Galaxy S24',
                'slug' => 'samsung-galaxy-s24',
                'short_description' => 'Premium Android smartphone',
                'description' => 'Samsung Galaxy S24 with AI features and exceptional camera quality.',
                'sku' => 'SAM24001',
                'price' => 799.00,
                'compare_price' => 899.00,
                'cost_price' => 600.00,
                'status' => 'active',
                'visibility' => 'visible',
                'weight' => 0.168,
                'is_featured' => true,
                'categories' => ['smartphones', 'electronics'],
                'primary_category' => 'smartphones'
            ],
            
            // Electronics - Laptops
            [
                'name' => 'MacBook Pro 16"',
                'slug' => 'macbook-pro-16',
                'short_description' => 'Professional laptop for creators',
                'description' => 'MacBook Pro 16-inch with M3 Pro chip, perfect for professional work.',
                'sku' => 'MBP16001',
                'price' => 2499.00,
                'compare_price' => 2699.00,
                'cost_price' => 1800.00,
                'status' => 'active',
                'visibility' => 'visible',
                'weight' => 2.140,
                'is_featured' => true,
                'categories' => ['laptops', 'electronics'],
                'primary_category' => 'laptops'
            ],
            [
                'name' => 'Dell XPS 13',
                'slug' => 'dell-xps-13',
                'short_description' => 'Ultrabook for professionals',
                'description' => 'Dell XPS 13 with Intel Core i7 and premium build quality.',
                'sku' => 'DELLXPS001',
                'price' => 1299.00,
                'cost_price' => 950.00,
                'status' => 'active',
                'visibility' => 'visible',
                'weight' => 1.290,
                'categories' => ['laptops', 'electronics'],
                'primary_category' => 'laptops'
            ],

            // Clothing - Men's
            [
                'name' => 'Men\'s Classic T-Shirt',
                'slug' => 'mens-classic-tshirt',
                'short_description' => 'Comfortable cotton t-shirt',
                'description' => 'High-quality cotton t-shirt for everyday wear.',
                'sku' => 'TSHIRT001',
                'price' => 29.99,
                'compare_price' => 39.99,
                'cost_price' => 15.00,
                'status' => 'active',
                'visibility' => 'visible',
                'weight' => 0.200,
                'categories' => ['mens-clothing', 'clothing'],
                'primary_category' => 'mens-clothing'
            ],
            [
                'name' => 'Men\'s Denim Jeans',
                'slug' => 'mens-denim-jeans',
                'short_description' => 'Classic blue denim jeans',
                'description' => 'Premium denim jeans with comfortable fit.',
                'sku' => 'JEANS001',
                'price' => 79.99,
                'compare_price' => 99.99,
                'cost_price' => 40.00,
                'status' => 'active',
                'visibility' => 'visible',
                'weight' => 0.650,
                'is_featured' => true,
                'categories' => ['mens-clothing', 'clothing'],
                'primary_category' => 'mens-clothing'
            ],

            // Clothing - Women's
            [
                'name' => 'Women\'s Summer Dress',
                'slug' => 'womens-summer-dress',
                'short_description' => 'Elegant summer dress',
                'description' => 'Beautiful floral summer dress perfect for any occasion.',
                'sku' => 'DRESS001',
                'price' => 89.99,
                'compare_price' => 119.99,
                'cost_price' => 45.00,
                'status' => 'active',
                'visibility' => 'visible',
                'weight' => 0.300,
                'is_featured' => true,
                'categories' => ['womens-clothing', 'clothing', 'accessories'],
                'primary_category' => 'womens-clothing'
            ],

            // Home & Garden - Furniture
            [
                'name' => 'Ergonomic Office Chair',
                'slug' => 'ergonomic-office-chair',
                'short_description' => 'Comfortable office chair',
                'description' => 'Ergonomic office chair with lumbar support and adjustable height.',
                'sku' => 'CHAIR001',
                'price' => 299.99,
                'compare_price' => 399.99,
                'cost_price' => 180.00,
                'status' => 'active',
                'visibility' => 'visible',
                'weight' => 15.500,
                'length' => 65.00,
                'width' => 65.00,
                'height' => 110.00,
                'categories' => ['furniture', 'home-garden'],
                'primary_category' => 'furniture'
            ],

            // Home & Garden - Kitchen
            [
                'name' => 'Stainless Steel Coffee Maker',
                'slug' => 'stainless-steel-coffee-maker',
                'short_description' => 'Premium coffee maker',
                'description' => 'Professional-grade coffee maker with programmable features.',
                'sku' => 'COFFEE001',
                'price' => 199.99,
                'compare_price' => 249.99,
                'cost_price' => 120.00,
                'status' => 'active',
                'visibility' => 'visible',
                'weight' => 3.200,
                'categories' => ['kitchen', 'home-garden'],
                'primary_category' => 'kitchen'
            ],
        ];

        foreach ($products as $productData) {
            $categories = $productData['categories'];
            $primaryCategory = $productData['primary_category'];
            unset($productData['categories'], $productData['primary_category']);

            $product = Product::create($productData);

            // Attach categories
            foreach ($categories as $index => $categorySlug) {
                $category = Category::where('slug', $categorySlug)->first();
                if ($category) {
                    $isPrimary = ($categorySlug === $primaryCategory);
                    $product->categories()->attach($category->id, [
                        'is_primary' => $isPrimary,
                        'sort_order' => $index + 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}