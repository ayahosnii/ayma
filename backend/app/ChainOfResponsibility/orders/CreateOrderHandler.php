<?php

namespace App\ChainOfResponsibility\orders;

use App\Models\Order;

class CreateOrderHandler extends AbstractOrderProcessHandler {
    public function handle($request, Order $order = null) {
        // Create the order with the given request data
        $order = Order::create($request->except('products'));
        echo "Order created.\n";

        return parent::handle($request, $order);
    }
}
