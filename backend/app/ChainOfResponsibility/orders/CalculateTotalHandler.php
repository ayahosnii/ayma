<?php

namespace App\ChainOfResponsibility\orders;

use App\Http\Requests\CreateOrderRequest;
use App\Models\Order;

class CalculateTotalHandler extends AbstractOrderProcessHandler {
    public function handle($request, Order $order = null) {
        $total = $order->orderItems->sum('total');
        $order->total_amount = $total;
        $order->save();

        echo "Total price calculated: $total.\n";
        return parent::handle($request, $order); // End of the chain
    }
}
