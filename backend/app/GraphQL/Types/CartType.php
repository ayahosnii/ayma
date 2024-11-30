<?php

namespace App\GraphQL\Types;

use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;
use GraphQL\Type\Definition\Type;

class CartType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Cart',
        'description' => 'Shopping Cart'
    ];

    public function fields(): array
    {
        return [
            'products' => [
                'type' => Type::listOf(GraphQL::type('Product')),
                'description' => 'List of products in the cart',
            ],
            'total_price' => [
                'type' => Type::int(), // Scalar value for total price
                'description' => 'Total price of the products in the cart',
            ],
            'currency' => [
                'type' => Type::string(), // Scalar value for currency
                'description' => 'Currency used for the cart',
            ],
        ];
    }
}
