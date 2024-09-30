<?php

namespace App\Services\Refund\RefundStrategies;

use App\Models\Order;

class PartialRefundStrategy implements RefundStrategy
{
    protected $items;

    /**
     * Create a new strategy instance.
     *
     * @param array $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    public function processRefund($order, float $amount, string $status = 'pending')
    {
        // Validate and apply partial refund to specific items
        foreach ($this->items as $item) {
            // Assuming OrderItem is a model that relates to the order items
            $orderItem = $order->orderItems()->where('product_id', $item['product_id'])->first();
            $product = $orderItem->product;  // Assuming there's a relationship between OrderItem and Product

            if (!$orderItem) {
                // Item not found
                throw new \Exception("Product ID {$item['product_id']} not found in order.");
            }

            if (!$product->refundable) {
                // Product is not refundable
                throw new \Exception("Product ID {$item['product_id']} is not eligible for a refund.");
            }

            if ($orderItem->quantity < $item['quantity']) {
                // Insufficient quantity
                throw new \Exception("Insufficient quantity for Product ID {$item['product_id']}. Requested: {$item['quantity']}, Available: {$orderItem->quantity}.");
            }

            // Process refund for the specific item
            $orderItem->quantity -= $item['quantity'];
            $orderItem->save();
        }

        // Additional logic to handle the total partial refund
        $order->refunded_amount += $amount;
        $order->save();
    }
}
