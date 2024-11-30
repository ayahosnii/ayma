<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ProductType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Product',
        'description' => 'A product type',
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::string(),
                'description' => 'The ID of the product',
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of the product',
            ],
            'sku' => [
                'type' => Type::string(),
                'description' => 'The SKU of the product',
            ],
            'slug' => [
                'type' => Type::string(),
                'description' => 'The slug of the product',
            ],
            'price' => [
                'type' => Type::float(), // Convert string to float
                'description' => 'The price of the product',
                'resolve' => function ($root) {
                    return (float) $root->price; // Ensure the price is cast as a float
                },
            ],
            'discount_price' => [
                'type' => Type::float(), // Convert string to float
                'description' => 'The discounted price of the product',
                'resolve' => function ($root) {
                    return (float) $root->discount_price;
                },
            ],
            'short_description' => [
                'type' => Type::string(),
                'description' => 'A short description of the product',
            ],
            'description' => [
                'type' => Type::string(),
                'description' => 'The description of the product',
            ],
            'stock' => [
                'type' => Type::int(), // Convert string to integer
                'description' => 'The stock count of the product',
                'resolve' => function ($root) {
                    return (int) $root->stock;
                },
            ],
            'low_stock_threshold' => [
                'type' => Type::int(),
                'description' => 'The threshold at which stock is considered low',
            ],
            'is_featured' => [
                'type' => Type::boolean(), // Convert string to boolean
                'description' => 'Whether the product is featured',
                'resolve' => function ($root) {
                    return (bool) $root->is_featured;
                },
            ],
            'color_id' => [
                'type' => Type::int(),
                'description' => 'The ID of the color associated with the product',
            ],
            'size_id' => [
                'type' => Type::int(),
                'description' => 'The ID of the size associated with the product',
            ],
            'category_id' => [
                'type' => Type::string(), // Keep as string
                'description' => 'The category ID of the product',
            ],
            'images' => [
                'type' => Type::listOf(Type::string()), // Define as a list of strings
                'description' => 'The list of product image URLs',
                'resolve' => function ($root) {
                    // Convert the collection of ProductImage objects to an array of image paths
                    return $root->images->map(function ($image) {
                        return $image->image_path; // Return only the `image_path`
                    })->toArray();
                },
            ],
            'first_image' => [
                'type' => Type::string(), // Single string for the first image URL
                'description' => 'The first image URL of the product',
                'resolve' => function ($root) {
                    // Check if the images relationship has any data and return the first image's path
                    $firstImage = $root->images->first(); // Get the first image from the collection
                    return $firstImage ? $firstImage->image_path : null; // Return the image path or null
                },
            ],

            'quantity' => [
                'type' => Type::int(),
                'description' => 'The quantity of this product in the cart',
            ],
            'updated_at' => [
                'type' => Type::string(), // Dates can be strings
                'description' => 'The last update timestamp',
            ],
            'created_at' => [
                'type' => Type::string(),
                'description' => 'The creation timestamp',
            ],
        ];
    }
}
