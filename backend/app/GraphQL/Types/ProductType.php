<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ProductType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Product',
        'description' => 'A type representing a product'
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
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
            'description' => [
                'type' => Type::string(),
                'description' => 'The description of the product',
            ],
            'short_description' => [
                'type' => Type::string(),
                'description' => 'A short description of the product',
            ],
            'price' => [
                'type' => Type::float(),
                'description' => 'The price of the product',
            ],
            'discount_price' => [
                'type' => Type::float(),
                'description' => 'The discount price of the product',
            ],
            'stock' => [
                'type' => Type::int(),
                'description' => 'The stock quantity of the product',
            ],
            'low_stock_threshold' => [
                'type' => Type::int(),
                'description' => 'The threshold at which stock is considered low',
            ],
            'is_featured' => [
                'type' => Type::boolean(),
                'description' => 'Whether the product is featured',
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
                'type' => Type::int(),
                'description' => 'The ID of the category the product belongs to',
            ],
        ];
    }
}
