<?php

namespace App\GraphQL\Services;

use App\Models\MongoModels\ProductsMongo;
use Illuminate\Support\Facades\Cache;

class CartService
{
    /**
     * Retrieve products for the user's cart.
     *
     * @param array $products
     * @return array
     * @throws \Exception
     */
    public function getProductsForCart($products)
    {
        // 3. Extract product IDs from the products array
        $productIds = array_map(function($product) {
            return $product['product_id']; // Return the 'product_id' from each product
        }, $products);

        // 4. Retrieve product details from MongoDB based on the product IDs
        $dbProducts = ProductsMongo::find($productIds); // Fetch the products from MongoDB

        // 5. Check if all requested products exist in the database
        if ($dbProducts->count() !== count($productIds)) {
            // If any product is not found, throw an exception
            throw new \Exception("Some products not found.");
        }

        return $dbProducts;
    }

    /**
     * Update the cart with the given products and quantities.
     *
     * @param array $args
     * @param \Illuminate\Database\Eloquent\Collection $dbProducts
     * @return array
     * @throws \Exception
     */
    public function updateCartWithProducts($args, $dbProducts)
    {
        // Get the authenticated user ID
        $userId = auth()->id();

        $cartKey = "cart:$userId"; // Cache key is unique to the user

        // Retrieve the list of products and quantities from the arguments
        $products = $args['products']; // Array of products to be added to the cart

        // 6. Retrieve the current cart items from cache
        $cartItems = Cache::get($cartKey, []); // Default to an empty array if no cart is found

        // Initialize variables to track total price and quantity in the cart
        $totalPrice = 0;
        $totalQuantity = 0;

        // 7. Loop through each product and update the cart
        foreach ($products as $productData) {
            $productId = $productData['product_id']; // Get product ID
            $quantityToAdd = $productData['quantity']; // Get the quantity to add

            // Find the corresponding product in the fetched MongoDB products
            $product = $dbProducts->where('id', $productId)->first(); // Find the product by its ID

            // 8. Check if the requested quantity is available in stock
            if ($product->stock < $quantityToAdd) {
                // If stock is insufficient, throw an exception
                throw new \Exception("Not enough stock for product: " . $product->name);
            }

            // 9. Update the cart items
            if (isset($cartItems[$productId])) {
                // If the product already exists in the cart, increase the quantity
                $cartItems[$productId] += $quantityToAdd;
            } else {
                // Otherwise, add the product to the cart with the specified quantity
                $cartItems[$productId] = $quantityToAdd;
            }

            // 10. Update the total price and total quantity for the cart
            $totalPrice += $product->price * $quantityToAdd; // Calculate total price
            $totalQuantity += $quantityToAdd; // Update total quantity
        }

        // 11. Store the updated cart back to the cache (1 hour expiration)
        Cache::put($cartKey, $cartItems, 3600); // Cache the cart for 1 hour

        return ['totalPrice' => $totalPrice, 'totalQuantity' => $totalQuantity, 'cartItems' => $cartItems];
    }
}
