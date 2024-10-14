<?php

namespace App\Services\Refund\RefundStrategies;

class StoreCreditStrategy implements RefundStrategy
{
    public function processRefund($order, float $amount)
    {
        // Logic for processing a store credit refund
        $order->store_credit += $amount;
        $order->save();
        // Additional logic to handle store credit refund
    }
}
