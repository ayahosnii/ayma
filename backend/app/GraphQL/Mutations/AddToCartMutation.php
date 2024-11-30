<?php

namespace App\GraphQL\Mutations;

use App\GraphQL\Services\CartService;
use App\Models\Product;
use Rebing\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;

class AddToCartMutation extends Mutation
{
    // Set the name of the mutation
    protected $attributes = [
        'name' => 'addToCart', // The name of the mutation
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
            // Define a single argument 'products'
            'products' => [
                'name' => 'products', // This is the name of the argument that will be used in the GraphQL query
                'type' => Type::nonNull(Type::listOf(\GraphQL::type('ProductInput'))), // List of products
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
        $cartService = new CartService();

        // Add products to the cart using the service
        $dbProducts = $cartService->getProductsForCart($args['products']);

        // Update the cart with the selected products
        $cartData = $cartService->updateCartWithProducts($args, $dbProducts);

        // 12. Prepare the updated cart data (including product details)
        $cartItems = $cartData['cartItems']; // Get the cart items from the service response
        $totalPrice = $cartData['totalPrice']; // Get total price from the service response
        $totalQuantity = $cartData['totalQuantity']; // Get total quantity from the service response

        // 13. Fetch product details for the updated cart
        $cartProducts = Product::find(array_keys($cartItems)); // Fetch product details using the cart item IDs

        $mappedProducts = $cartProducts->map(function($product) use ($cartItems) {
            // Add quantity to the product object
            $product->quantity = $cartItems[$product->id]; // Map quantity to each product
            return $product;
        });

// Prepare the response structure to include quantity
        $response = [
            'products' => $mappedProducts, // The products with their mapped quantities
            'quantity' => $mappedProducts->pluck('quantity'), // Extract quantity values into a separate array
            'total_price' => $totalPrice,  // Assuming you have a total price variable
            'currency' => 'USD',          // Assuming USD currency, you can adjust this as needed
        ];

// Return the response (or pass it to the GraphQL mutation response)
        return $response;
    }
}
