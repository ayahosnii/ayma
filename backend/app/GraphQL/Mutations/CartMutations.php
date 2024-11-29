<?php

namespace App\GraphQL\Mutations;

use Rebing\GraphQL\Support\Mutation;
use Illuminate\Support\Facades\Cache;

class CartMutations extends Mutation
{
    // Define the attributes for the mutation
    protected $attributes = [
        'name' => 'cartMutations', // The name of the mutation
    ];

    /**
     * Define the return type of the mutation
     *
     * @return \GraphQL\Type\Definition\Type
     */
    public function type(): \GraphQL\Type\Definition\Type
    {
        return \GraphQL::type('Cart'); // Returns the custom GraphQL type 'Cart'
    }

    /**
     * Define the arguments required by the mutation
     *
     * @return array
     */
    public function args(): array
    {
        return [
            // Argument: Product ID (required, string type)
            'product_id' => [
                'type' => \GraphQL\Type\Definition\Type::nonNull(\GraphQL\Type\Definition\Type::string()),
                'description' => 'The ID of the product',
            ],
            // Argument: Quantity (required, integer type)
            'quantity' => [
                'type' => \GraphQL\Type\Definition\Type::nonNull(\GraphQL\Type\Definition\Type::int()),
                'description' => 'The quantity of the product',
            ],
        ];
    }

    /**
     * Resolver function to handle the mutation logic
     *
     * @param mixed $root
     * @param array $args
     * @return array
     */
    public function resolve($root, $args)
    {
        // 1. Retrieve the authenticated user's ID
        $userId = auth()->id();
        $cartKey = "cart:$userId"; // Generate a unique cache key for the user's cart

        // 2. Fetch the existing cart from the cache (default is an empty array)
        $cart = Cache::get($cartKey, []);

        // Extract the provided product ID and quantity from arguments
        $productId = $args['product_id'];
        $quantity = $args['quantity'];

        // 3. Update the cart based on the provided quantity
        if ($quantity <= 0) {
            // If quantity is zero or negative, remove the product from the cart
            unset($cart[$productId]);
        } else {
            // Otherwise, add or update the product in the cart with the new quantity
            $cart[$productId] = $quantity;
        }

        // 4. Save the updated cart back to the cache
        Cache::put($cartKey, $cart);

        // 5. Prepare the response: calculate totals and fetch product details
        $totalQuantity = array_sum($cart); // Total quantity of items in the cart
        $totalPrice = 0; // Initialize the total price

        // Retrieve product details to calculate the total price
        $productIds = array_keys($cart); // Get all product IDs from the cart
        $products = \App\Models\MongoModels\ProductsMongo::find($productIds); // Fetch products from the database

        // Prepare a list of products to return in the response
        $cartProducts = [];
        foreach ($products as $product) {
            $productId = (string) $product->_id; // Convert MongoDB ID to string
            $productQuantity = $cart[$productId]; // Get the quantity of this product in the cart

            // Add the product details to the response
            $cartProducts[] = [
                'id' => $productId,
                'name' => $product->name, // Product name
                'price' => (float) $product->price, // Product price
                'discount_price' => (float) $product->discount_price, // Discounted price, if available
                'quantity' => $productQuantity, // Quantity in the cart
            ];

            // Increment the total price
            $totalPrice += $product->price * $productQuantity;
        }

        // 6. Return the cart data in the expected response format
        return [
            'products' => $cartProducts, // List of products in the cart
            'total_price' => $totalPrice, // Total price of items in the cart
            'total_quantity' => $totalQuantity, // Total quantity of items in the cart
            'currency' => 'USD', // Hardcoded currency
        ];
    }
}
