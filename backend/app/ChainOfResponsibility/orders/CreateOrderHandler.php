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

        // Generate a unique transaction ID
        $transactionId = $this->generateTransactionId();

        // Add the transaction ID to the order data
        $orderData['transaction_id'] = $transactionId;

        // Create the order with the provided data
        $order = Order::create($orderData);

        // Continue to the next handler in the chain
        return parent::handle($request, $order);
    }


    /**
     * Generate a unique transaction ID.
     *
     * @return string
     */
    protected function generateTransactionId()
    {
        return strtoupper(uniqid('TXN_'));
    }
}
