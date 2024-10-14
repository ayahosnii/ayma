<?php

namespace App\Services\Refund\RefundStrategies;

use App\Models\Refund;
use App\Models\RefundItem;
use Illuminate\Support\Facades\Mail;

class FullRefundStrategy implements RefundStrategy
{
    public function processRefund($order, float $amount, string $status = 'pending')
    {
        // Create a new refund record
        $refund = Refund::create([
            'order_id' => $order->id,
            'amount' => $amount,
            'status' => $status,
        ]);

        if ($status === 'approved') {
            $order->status = 'refunded';
            // Process the actual refund
            $this->processActualRefund($order, $amount);

            // Record refunded items
            foreach ($order->items as $item) {
                RefundItem::create([
                    'refund_id' => $refund->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                ]);
            }
        } else {
            $order->status = 'pending'; // Set status to pending if not approved
        }

        $order->save();

        // Notify the customer about the refund status
        $this->notifyCustomer($order, $status);
    }
    private function processActualRefund($order, float $amount)
    {
        // Logic to process the actual refund (e.g., payment gateway API call)
        // For example:
        // PaymentGateway::refund($order->payment_id, $amount);

        // Update inventory after refund approval
        $this->updateInventory($order);
    }

    private function notifyCustomer($order, string $status)
    {
        // Logic to notify the customer about the refund status
        // E.g., sending an email or SMS notification
        Mail::to($order->customer_email)->send(new \App\Mail\RefundProcessed($order, $status));
    }

    private function updateInventory($order)
    {
        // Logic to update inventory based on the refunded items
        foreach ($order->items as $item) {
            $product = \App\Models\Product::find($item->product_id);
            $product->increment('stock', $item->quantity);
        }
    }
}
