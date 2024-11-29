<?php

namespace App\GraphQL\Mutations;

use App\Models\MongoModels\ProductsMongo;
use Rebing\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Cache;

class RemoveFromCartMutation extends Mutation
{
    // Set the name of the mutation
    protected $attributes = [
        'name' => 'removeFromCart', // The name of the mutation
    ];

    /**
     * Define the return type of the mutation
     *
     * @return Type
     */
    public function type(): Type
    {
        return \GraphQL::type('Cart'); // Return the Cart type for GraphQL response
    }

    /**
     * Define the arguments required for the mutation
     *
     * @return array
     */
    public function args(): array
    {
        return [
            'products' => [
                'name' => 'products', // This is the name of the argument that will be used in the GraphQL query
                'type' => Type::nonNull(Type::listOf(\GraphQL::type('ProductInput'))), // List of products to remove
            ],
        ];
    }

    /**
     * Resolve the mutation logic (handling the cart operations)
     *
     * @param mixed $root
     * @param array $args
     * @return array
     */
    public function resolve($root, $args)
    {
        // 1. Get the authenticated user ID
        $userId = auth()->id(); // Retrieve the authenticated user's ID
        $cartKey = "cart:$userId"; // Cache key is unique to the user

        // 2. Retrieve the list of products and quantities from the arguments
        $products = $args['products']; // Array of products to be removed from the cart

        // 3. Retrieve the current cart items from cache
        $cartItems = Cache::get($cartKey, []); // Default to an empty array if no cart is found

        // Initialize variables to track total price and quantity in the cart
        $totalPrice = 0;
        $totalQuantity = 0;

        // 4. Loop through each product and update the cart
        foreach ($products as $productData) {
            $productId = $productData['product_id']; // Get product ID
            $quantityToRemove = $productData['quantity']; // Get the quantity to remove

            // 5. Check if the product exists in the cart
            if (!isset($cartItems[$productId])) {
                throw new \Exception("Product not found in cart: " . $productId);
            }

            // 6. Check if the quantity to remove is valid
            if ($cartItems[$productId] < $quantityToRemove) {
                throw new \Exception("Not enough quantity to remove for product: " . $productId);
            }

            // 7. Remove the quantity from the cart
            $cartItems[$productId] -= $quantityToRemove;

            // If the quantity reaches zero, remove the product from the cart
            if ($cartItems[$productId] <= 0) {
                unset($cartItems[$productId]);
            }

            // 8. Update the total price and total quantity for the cart
            $product = ProductsMongo::find($productId);
            $totalPrice -= $product->price * $quantityToRemove; // Subtract from total price
            $totalQuantity -= $quantityToRemove; // Subtract from total quantity
        }

        // 9. Store the updated cart back to the cache (1 hour expiration)
        Cache::put($cartKey, $cartItems, 3600); // Cache the cart for 1 hour

        // 10. Prepare the updated cart data (including product details)
        $cartProducts = ProductsMongo::find(array_keys($cartItems)); // Fetch product details for the updated cart

        // 11. Map the products with their quantities
        $mappedProducts = $cartProducts->map(function($product) use ($cartItems) {
            $product->quantity = $cartItems[$product->id]; // Map quantity to each product
            return $product;
        });

        // Return the updated cart data, including total price, total quantity, and currency
        return [
            'products' => $mappedProducts, // List of products in the cart with quantity
            'quantity' => array_values($cartItems), // Array of quantities for each product
            'total_price' => $totalPrice, // Total price of the products in the cart
            'total_quantity' => $totalQuantity, // Total quantity of products in the cart
            'currency' => 'USD', // Currency code (hardcoded to USD)
        ];
    }
}
