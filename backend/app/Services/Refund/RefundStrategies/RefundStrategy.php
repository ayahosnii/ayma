<?php

namespace App\Services\Refund\RefundStrategies;

interface RefundStrategy
{
    public function processRefund($order, float $amount, string $status = 'pending');
}
