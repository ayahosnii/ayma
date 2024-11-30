<?php

namespace App\GraphQL\Queries;

use App\Models\Product;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Illuminate\Support\Facades\Cache;

class CartQuery extends Query
{
    protected $attributes = [
        'name' => 'cart',
    ];

    public function type(): Type
    {
        return GraphQL::type('Cart'); // Refers to the CartType
    }

    public function resolve($root, $args)
    {
        $userId = auth()->id(); // Retrieve authenticated user ID
        $cartKey = "cart:$userId";

        // Fetch cart data from cache
        $cartItems = Cache::get($cartKey, []);

        if (empty($cartItems)) {
            return [
                'products' => [],
                'stock' => [],
                'quantity' => [],
                'total_price' => 0,
                'total_quantity' => 0,
                'currency' => 'USD',
            ];
        }

        // Fetch product details for the items in the cart
        $productIds = array_keys($cartItems);
        $products = Product::find($productIds);

        $totalPrice = 0;
        $totalQuantity = 0;

        // Build the product details array, including quantity
        $cartProducts = [];
        foreach ($products as $product) {
            $productId = (string) $product->id;
            $quantity = $cartItems[$productId] ?? 0;

            $cartProducts[] = [
                'id' => $productId,
                'name' => $product->name,
                'price' => (float) $product->price,
                'discount_price' => (float) $product->discount_price,
                'quantity' => $quantity,
            ];

            $totalPrice += $product->price * $quantity;
            $totalQuantity += $quantity;
        }

        return [
            'products' => $cartProducts,
            'stock' => array_column($cartProducts, 'quantity'),
            'quantity' => array_column($cartProducts, 'quantity'),
            'total_price' => $totalPrice,
            'total_quantity' => $totalQuantity,
            'currency' => 'USD',
        ];
    }
}
