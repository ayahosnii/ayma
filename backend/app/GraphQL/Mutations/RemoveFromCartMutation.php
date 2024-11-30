<?php

namespace App\GraphQL\Mutations;

use App\GraphQL\Services\CartService;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;

class RemoveFromCartMutation extends Mutation
{
    protected $attributes = [
        'name' => 'removeFromCart', // Mutation name
    ];

    public function type(): Type
    {
        return \GraphQL::type('Cart'); // Return the Cart type
    }

    public function args(): array
    {
        return [
            'product_id' => [
                'name' => 'product_id',
                'type' => Type::nonNull(Type::int()), // Required product ID to remove
            ],
            'quantity' => [
                'name' => 'quantity',
                'type' => Type::int(), // Optional quantity to remove
            ]
        ];
    }

    public function resolve($root, $args)
    {
        $cartService = new CartService();

        // Call the method to remove product or quantity from the cart
        if (isset($args['quantity'])) {
            // Remove specific quantity
            $cartData = $cartService->removeProductFromCart($args['product_id'], $args['quantity']);
        } else {
            // Remove product entirely
            $cartData = $cartService->removeProductFromCart($args['product_id']);
        }

        return $cartData;
    }
}
