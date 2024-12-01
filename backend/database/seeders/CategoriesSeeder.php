<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str; // For generating slugs
use App\Models\Category; // Import the Category model

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Define a list of parent categories and their respective child categories
        $categories = [
            [
                'name' => 'Electronics',
                'slug' => Str::slug('Electronics'),
                'description' => 'Electronic devices and accessories',
                'children' => [
                    ['name' => 'Mobile Phones', 'description' => 'Smartphones and feature phones'],
                    ['name' => 'Laptops', 'description' => 'Personal and business laptops'],
                    ['name' => 'Accessories', 'description' => 'Chargers, cables, and more'],
                ],
            ],
            [
                'name' => 'Groceries',
                'slug' => Str::slug('Groceries'),
                'description' => 'Everyday essentials and food items',
                'children' => [
                    ['name' => 'Fruits & Vegetables', 'description' => 'Fresh produce'],
                    ['name' => 'Dairy Products', 'description' => 'Milk, cheese, and more'],
                    ['name' => 'Beverages', 'description' => 'Juices, sodas, and water'],
                ],
            ],
            [
                'name' => 'Clothing',
                'slug' => Str::slug('Clothing'),
                'description' => 'Apparel for men, women, and children',
                'children' => [
                    ['name' => 'Men', 'description' => 'Men’s shirts, pants, and jackets'],
                    ['name' => 'Women', 'description' => 'Women’s dresses, skirts, and blouses'],
                    ['name' => 'Kids', 'description' => 'Clothing for children of all ages'],
                ],
            ],
        ];

        // Seed the categories and their children
        foreach ($categories as $parentCategory) {
            // Create the parent category
            $parent = Category::create([
                'name' => $parentCategory['name'],
                'slug' => $parentCategory['slug'],
                'description' => $parentCategory['description'],
                'image' => null, // You can add a default image path if needed
                'parent_id' => null,
                'order' => 0,
                'is_active' => true,
            ]);

            // Create the child categories
            foreach ($parentCategory['children'] as $childCategory) {
                Category::create([
                    'name' => $childCategory['name'],
                    'slug' => Str::slug($childCategory['name']),
                    'description' => $childCategory['description'],
                    'image' => null, // Optional: Add image if necessary
                    'parent_id' => $parent->id,
                    'order' => 0,
                    'is_active' => true,
                ]);
            }
        }
    }
}
