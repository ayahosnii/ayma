<?php

namespace App\GraphQL\Types;

use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;
use GraphQL\Type\Definition\Type;

class ProductQuantityType extends GraphQLType
{
    protected $attributes = [
        'name' => 'ProductQuantity', // Name of the new type
        'description' => 'Product and its quantity in the cart',
    ];

    public function fields(): array
    {
        return [
            'products' => [
                'type' => Type::listOf(GraphQL::type('ProductQuantity')), // List of product and quantity pairs
                'description' => 'List of products and their quantities in the cart',
            ],
            'quantity' => [
                'type' => Type::int(), // Quantity of the product in the cart
                'description' => 'Quantity of the product in the cart',
            ],
        ];
    }
}
