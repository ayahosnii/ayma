<?php

namespace App\ChainOfResponsibility\orders;

use App\Models\Order;

class CreateOrderHandler extends AbstractOrderProcessHandler
{
    public function handle($request, Order $order = null)
    {
        // Ensure that user_id is included in the request data
        $orderData = $request->except('products');

        // Validate that user_id is set
        if (!isset($orderData['user_id'])) {
            throw new \Exception('User ID is not set.');
        }

        // Create the order with the provided data
        $order = Order::create($orderData);
        echo "Order created.\n";

        // Continue to the next handler in the chain
        return parent::handle($request, $order);
    }
}
