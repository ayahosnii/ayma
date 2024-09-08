<?php

namespace App\ChainOfResponsibility\orders;

use App\Http\Requests\CreateOrderRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

class ProcessOrderItemsHandler extends AbstractOrderProcessHandler {
    public function handle($request, Order $order = null) {
        foreach ($request->products as $productData) {
            $product = Product::find($productData['product_id']);
            if (!$product) {
                return response()->json(['error' => 'Product not found'], 404); // Exit the chain
            }

            // Create an order item for each product
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $productData['quantity'],
                'price' => $product->price,
                'total' => $product->price * $productData['quantity'],
                'product_name' => $product->name ?? '',
                'product_description' => $product->description ?? '',
            ]);
        }

        echo "Order items processed.\n";
        return parent::handle($request, $order); // Pass the order to the next handler
    }
}
