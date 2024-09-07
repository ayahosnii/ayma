<?php

namespace App\ChainOfResponsibility\orders;

use App\Http\Requests\CreateOrderRequest;
use App\Models\Order;

interface OrderProcessHandler {
    public function setNext(OrderProcessHandler $handler): OrderProcessHandler;
    public function handle($request);
}
