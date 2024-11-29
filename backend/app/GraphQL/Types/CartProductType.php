<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class CartProductType extends ProductType
{
    protected $attributes = [
        'name' => 'CartProduct',
        'description' => 'A product in the cart with quantity and other cart-specific attributes',
    ];

    public function fields(): array
    {
        return array_merge(parent::fields(), [
            'quantity' => [
                'type' => Type::int(),
                'description' => 'The quantity of the product in the cart',
            ],
        ]);
    }
}
