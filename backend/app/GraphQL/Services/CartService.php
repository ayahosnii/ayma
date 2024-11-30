<?php

namespace App\GraphQL\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

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

        // 4. Retrieve product details from SQL database based on the product IDs
        $dbProducts = Product::find($productIds); // Fetch the products from SQL database

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

            // Find the corresponding product in the fetched SQL products
            $product = $dbProducts->where('id', $productId)->first(); // Find the product by its ID

            // 8. Retrieve the cached stock for the product, or fall back to database if not cached
            $cachedStock = Cache::get('product_stock_' . $productId, $product->stock);

            Log::info('Product Stock (Cached): ' . $cachedStock);
            Log::info('Quantity to Add: ' . $quantityToAdd);

            // Check if the requested quantity is available in stock (cached or DB)
            if ($cachedStock < $quantityToAdd) {
                Log::error("Not enough stock for product: " . $product->name);
                throw new \Exception("Not enough stock for product: " . $product->name);
            }

            // Check if product quantity in cart exceeds available stock
            if (isset($cartItems[$productId])) {
                $currentCartQuantity = $cartItems[$productId];
                if ($currentCartQuantity + $quantityToAdd > $cachedStock) {
                    Log::error("Not enough stock to add requested quantity of " . $product->name);
                    throw new \Exception("Not enough stock for product: " . $product->name);
                }
                // If stock is available, add to existing cart quantity
                $cartItems[$productId] += $quantityToAdd;
            } else {
                // Otherwise, add the product to the cart with the specified quantity
                if ($quantityToAdd > $cachedStock) {
                    Log::error("Not enough stock to add requested quantity of " . $product->name);
                    throw new \Exception("Not enough stock for product: " . $product->name);
                }
                $cartItems[$productId] = $quantityToAdd;
            }

            // 10. Update the total price and total quantity for the cart
            $totalPrice += $product->price * $quantityToAdd; // Calculate total price
            $totalQuantity += $quantityToAdd; // Update total quantity
        }

        // 11. Store the updated cart back to the cache (1 hour expiration)
        Cache::put($cartKey, $cartItems, 3600); // Cache the cart for 1 hour

        // Optionally, update the cached stock to reflect changes in quantity
        foreach ($products as $productData) {
            $productId = $productData['product_id'];
            // Decrease the stock in cache based on the quantity added to the cart
            $cachedStock = Cache::get('product_stock_' . $productId, $dbProducts->where('id', $productId)->first()->stock);
            $newStock = $cachedStock - $productData['quantity'];
            Cache::put('product_stock_' . $productId, $newStock, 3600);  // Update cache with new stock value
        }

        return ['totalPrice' => $totalPrice, 'totalQuantity' => $totalQuantity, 'cartItems' => $cartItems];
    }

    /**
     * Remove a specific product from the cart or reduce its quantity.
     *
     * @param int $productId
     * @param int|null $quantityToRemove
     * @return array
     * @throws \Exception
     */
    public function removeProductFromCart($productId, $quantityToRemove = null)
    {
        $userId = auth()->id();
        $cartKey = "cart:$userId"; // Unique cache key for the user

        // Retrieve the current cart items from the cache
        $cartItems = Cache::get($cartKey, []);

        // Check if the product exists in the cart
        if (!isset($cartItems[$productId])) {
            throw new \Exception("Product not found in the cart.");
        }

        // Get the product to check for stock and price
        $product = Product::find($productId);

        // If no quantity is specified, remove the product entirely
        if ($quantityToRemove === null) {
            // Remove the product from the cart
            unset($cartItems[$productId]);
            Log::info("Product removed from cart: " . $product->name);
        } else {
            // If quantity to remove is provided, reduce it
            $currentQuantity = $cartItems[$productId];

            if ($quantityToRemove > $currentQuantity) {
                throw new \Exception("Cannot remove more than the current quantity in the cart.");
            }

            // Reduce the quantity in the cart
            $cartItems[$productId] -= $quantityToRemove;

            // If the quantity reaches zero, remove the product
            if ($cartItems[$productId] <= 0) {
                unset($cartItems[$productId]);
            }

            Log::info("Removed $quantityToRemove of " . $product->name . " from the cart.");
        }

        // Update the cart in cache
        Cache::put($cartKey, $cartItems, 3600); // Cache cart for 1 hour

        // Update total price and quantity
        return $this->updateCartTotals($cartItems);
    }

    /**
     * Remove all products from the cart.
     *
     * @return array
     */
    public function removeAllProductsFromCart()
    {
        $userId = auth()->id();
        $cartKey = "cart:$userId";

        // Clear the cart
        Cache::forget($cartKey);

        // Return the cart totals after clearing
        return [
            'totalPrice' => 0,
            'totalQuantity' => 0,
            'cartItems' => [],
        ];
    }

    /**
     * Update the total price and total quantity based on the current cart items.
     *
     * @param array $cartItems
     * @return array
     */
    private function updateCartTotals($cartItems)
    {
        $totalPrice = 0;
        $totalQuantity = 0;

        // Loop through cart items to calculate totals
        foreach ($cartItems as $productId => $quantity) {
            $product = Product::find($productId);
            $totalPrice += $product->price * $quantity;
            $totalQuantity += $quantity;
        }

        return [
            'totalPrice' => $totalPrice,
            'totalQuantity' => $totalQuantity,
            'cartItems' => $cartItems,
        ];
    }
}
