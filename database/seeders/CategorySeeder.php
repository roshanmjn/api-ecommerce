<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electronics',
                'slug' => 'electronics',
                'description' => 'Electronic devices and gadgets',
                'status' => 'active',
                'visibility' => 'visible',
                'sort_order' => 1,
                'children' => [
                    [
                        'name' => 'Smartphones',
                        'slug' => 'smartphones',
                        'description' => 'Mobile phones and accessories',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Laptops',
                        'slug' => 'laptops',
                        'description' => 'Portable computers',
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Headphones',
                        'slug' => 'headphones',
                        'description' => 'Audio devices',
                        'sort_order' => 3,
                    ],
                ]
            ],
            [
                'name' => 'Clothing',
                'slug' => 'clothing',
                'description' => 'Fashion and apparel',
                'status' => 'active',
                'visibility' => 'visible',
                'sort_order' => 2,
                'children' => [
                    [
                        'name' => 'Men\'s Clothing',
                        'slug' => 'mens-clothing',
                        'description' => 'Clothing for men',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Women\'s Clothing',
                        'slug' => 'womens-clothing',
                        'description' => 'Clothing for women',
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Accessories',
                        'slug' => 'accessories',
                        'description' => 'Fashion accessories',
                        'sort_order' => 3,
                    ],
                ]
            ],
            [
                'name' => 'Home & Garden',
                'slug' => 'home-garden',
                'description' => 'Home improvement and gardening',
                'status' => 'active',
                'visibility' => 'visible',
                'sort_order' => 3,
                'children' => [
                    [
                        'name' => 'Furniture',
                        'slug' => 'furniture',
                        'description' => 'Home furniture',
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Kitchen',
                        'slug' => 'kitchen',
                        'description' => 'Kitchen appliances and tools',
                        'sort_order' => 2,
                    ],
                ]
            ],
        ];

        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);

            $category = Category::create($categoryData);

            foreach ($children as $childData) {
                $childData['parent_id'] = $category->id;
                $childData['status'] = 'active';
                $childData['visibility'] = 'visible';
                Category::create($childData);
            }
        }
    }
}