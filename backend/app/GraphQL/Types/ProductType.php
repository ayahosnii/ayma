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
                    return $root->images; // Return the array as-is
                },
            ],
            'first_image' => [
                'type' => Type::string(), // Define as a single string type
                'description' => 'The first image URL of the product',
                'resolve' => function ($root) {
                    // Check if images exist and are not empty
                    if (is_array($root->images) && !empty($root->images)) {
                        // Decode the first image (assume it's a JSON string)
                        $firstImage = json_decode($root->images[0], true);

                        // Return the image path if decoding is successful and the key exists
                        if (is_array($firstImage) && isset($firstImage['image_path'])) {
                            return $firstImage['image_path'];
                        }
                    }

                    // Return null if no valid first image is found
                    return null;
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
